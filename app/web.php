<?php

//@TODO Associate mangas/animes to team
//@TODO Search via associated animes
//@TODO Search via description (in select2)
//@TODO Associate locale to User

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$app = require __DIR__.'/bootstrap.php';

// Controlers

$app->match('/', function (Request $request) use ($app) {
	$teams = TeamQuery::create()
		->filterByStatus(1)
		->find();
	$topten = TeamQuery::create()
		->topten()
		->find();

	$teams_array = array();
	foreach ($teams as $team) {
		$teams_array[$team->getId()] = $team->getName();
	}

	$form = $app['form.factory']->createBuilder('form')
	->add('teams', 'choice', array(
		'label'   => 'forms.add_teams',
		'multiple' => true,
		'choices' => $teams_array,
		'expanded' => false,
		'attr' => array(
			'placeholder' => 'placeholders.select_teams',
		)
	))
	->add('email', 'email', array(
		'label' => 'forms.give_email',
		'required' => true,
		'constraints' => array(
			new Assert\NotBlank(array('message' => 'validators.blank')),
			new Assert\Email(array('message' => 'validators.invalid_email'))
		),
		'attr' => array(
			'placeholder' => 'placeholders.email',
		)
	))
	->getForm();

	if ('POST' == $request->getMethod()) {
		$form->bind($request);

		if ($form->isValid()) {
			$data = $form->getData();
			$email = $data['email'];

			$existing_email = UserQuery::create()
				->filterByEmail($email)
				->findOne();

			if ($existing_email) {
				$content = $app['twig']->render('email/remember.twig', array(
					'code' => $existing_email->getCode(),
					'hash' => $existing_email->getHash(),
					'email' => $email,
					'server' => $_SERVER['SERVER_NAME']
				));

				$message = \Swift_Message::newInstance()
					->setContentType('text/html')
					->setSubject('[Mangalerts] Your alerts link')
					->setFrom(array($app['mailer.from']))
					->setTo(array($email))
					->setBody($content);

				$app['mailer']->send($message);$message = \Swift_Message::newInstance()
    				->setContentType('text/html')
					->setSubject('[Mangalerts] Your alerts link')
					->setFrom(array($app['mailer.from']))
					->setTo(array($email))
					->setBody($content);

				$app['mailer']->send($message);

                $app['session']->getFlashBag()->add(
                  'info',
                  array(
                    'message' => $app['translator']->trans('flash.info.already_used_email'),
                  )
                );
				return $app['twig']->render('template/home.twig', array(
						'teams' => $teams,
						'topten' => $topten,
						'form' => $form->createView())
				);
			}

			$user = new User();
			$user->setEmail($data['email']);

			$propel_teams = TeamQuery::create()
				->findPks($data['teams']);

			$user->setTeams($propel_teams);
			$user->save();

			$content = $app['twig']->render('email/new.twig', array(
				'code' => $user->getCode(),
				'hash' => $user->getHash(),
				'email' => $email,
				'server' => $_SERVER['SERVER_NAME']
			));

			$message = \Swift_Message::newInstance()
				->setContentType('text/html')
				->setSubject('[Mangalerts] Your alerts has been created')
				->setFrom(array($app['mailer.from']))
				->setTo(array($email))
				->setBody($content);

			$app['mailer']->send($message);

            $app['session']->getFlashBag()->add(
              'info',
              array(
                'message' => $app['translator']->trans('flash.success.alert_created'),
              )
            );
			return $app->redirect($app['url_generator']->generate('homepage'));
		}
	}

	return $app['twig']->render('template/home.twig', array(
		'teams' => $teams,
		'topten' => $topten,
		'form' => $form->createView())
	);
})
->bind('homepage');

$app->get('/alert/{code}/{hash}/delete', function (Request $request, $code, $hash) use ($app) {
	$user = UserQuery::create()
		->filterByCode($code)
		->_and()
		->filterByHash($hash)
		->_and()
		->filterByStatus(1)
		->findOne();

	if ($user){
        $app['session']->getFlashBag()->add(
          'info',
          array(
            'message' => $app['translator']->trans('flash.success.alert_deleted'),
          )
        );
		$user->delete();
	} else {
        $app['session']->getFlashBag()->add(
          'info',
          array(
            'message' => $app['translator']->trans('flash.error.alert_deletion'),
          )
        );
		$app->abort(404, 'Code and hash not recognized.');
	}

	return $app->redirect($app['url_generator']->generate('homepage'));
})
->bind('alert_delete');

$app->get('/alert/{code}/{hash}/activate', function (Request $request, $code, $hash) use ($app) {
	$user = UserQuery::create()
		->filterByCode($code)
		->_and()
		->filterByHash($hash)
		->findOne();

	if ($user){
        $app['session']->getFlashBag()->add(
          'info',
          array(
            'message' => $app['translator']->trans('flash.success.alert_activated'),
          )
        );
		$user->activate(true);
	} else {
        $app['session']->getFlashBag()->add(
          'info',
          array(
            'message' => $app['translator']->trans('flash.error.alert_activation'),
          )
        );
		$app->abort(404, 'Code and hash not recognized.');
	}

	return $app->redirect($app['url_generator']->generate('alert_edit', array('code' => $code, 'hash' => $hash)));
})
->bind('alert_delete');

$app->match('/alert/{code}/{hash}', function (Request $request, $code, $hash) use ($app) {
	$user = UserQuery::create()
		->filterByCode($code)
		->_and()
		->filterByHash($hash)
		->_and()
		->filterByStatus(1)
		->findOne();

	if (!$user) {
		$app->abort(404, 'Code and hash not recognized.');
	}

	$teams = TeamQuery::create()
		->filterByStatus(1)
		->find();
	$topten = TeamQuery::create()
		->topten()
		->find();

	$teams_array = array();
	foreach ($teams as $team) {
		$teams_array[$team->getId()] = $team->getName();
	}

	$user_teams = $user->getTeams();
	$team_ids = array();
	foreach ($user_teams as $user_team) {
		$team_ids[] = $user_team->getId();
	}

	$datas = array(
		'email' => $user->getEmail(),
		'id' => $user->getId(),
		'teams' => $team_ids
	);

	$form = $app['form.factory']->createBuilder('form')
		->add('teams', 'choice', array(
		'label'   => 'forms.the_teams',
		'multiple' => true,
		'choices' => $teams_array,
		'expanded' => false
	))
		->getForm();

	$form->setData($datas);

	if ('POST' == $request->getMethod()) {
		$form->bind($request);

		if ($form->isValid()) {
			$data = $form->getData();

			$propel_teams = TeamQuery::create()
				->findPks($data['teams']);

			$user->setTeams($propel_teams);
			$user->save();

            $app['session']->getFlashBag()->add(
              'info',
              array(
                'message' => $app['translator']->trans('flash.success.alert_edited'),
              )
            );
		}
	}

	return $app['twig']->render('template/edit.twig', array(
			'teams' => $teams,
			'topten' => $topten,
			'code' => $code,
			'hash' => $hash,
			'form' => $form->createView())
	);
})
->bind('alert_edit');

$app->match('/team/submit', function (Request $request) use ($app) {
	$form = $app['form.factory']->createBuilder('form')
		->add('name', 'text', array(
		'label' => 'forms.team_name',
		'required' => true,
		'constraints' => array(
			new Assert\NotBlank(array('message' => 'validators.blank'))
		)
	))
		->add('email', 'email', array(
		'label' => 'forms.team_email',
		'required' => true,
		'constraints' => array(
			new Assert\NotBlank(array('message' => 'validators.blank')),
			new Assert\Email(array('message' => 'validators.invalid_email'))
		),
		'attr' => array(
			'placeholder' => 'placeholders.email'
		)
	))
		->add('url', 'url', array(
		'label' => 'forms.team_url',
		'required' => true,
		'constraints' => array(
			new Assert\NotBlank(array('message' => 'Don\'t leave blank')),
			new Assert\Url(array('message' => 'Invalid URL'))
		),
		'attr' => array(
			'placeholder' => 'http://'
		)
	))
		->add('feed', 'url', array(
		'label' => 'forms.team_feed',
		'required' => true,
		'constraints' => array(
			new Assert\NotBlank(array('message' => 'Don\'t leave blank')),
			new Assert\Url(array('message' => 'Invalid URL'))
		),
		'attr' => array(
			'placeholder' => 'http://'
		)
	))
		->add('description', 'textarea', array(
		'label' => 'forms.team_description',
		'required' => false
	))
		->getForm();

	if ('POST' == $request->getMethod()) {
		$form->bind($request);

		if ($form->isValid()) {
			$data = $form->getData();

			$team = new Team();
			$team->setName($data['name']);
			$team->setEmail($data['email']);
			$team->setUrl($data['url']);
			$team->setFeed($data['feed']);
			$team->setDescription($data['description']);
			$team->save();
            
            $content = $app['twig']->render('email/new_team.twig', array(
    			'team' => $team,
				'server' => $_SERVER['SERVER_NAME']
			));
            
            $message = \Swift_Message::newInstance()
    				->setContentType('text/html')
					->setSubject('[Mangalerts] New team added')
					->setFrom(array($app['mailer.from']))
					->setTo(array($app['mailer.error.receiver']))
					->setBody($content);

				$app['mailer']->send($message);

            $app['session']->getFlashBag()->add(
              'info',
              array(
                'message' => $app['translator']->trans('flash.success.team_created'),
              )
            );
			return $app->redirect($app['url_generator']->generate('team_submit'));
		}
	}
	return $app['twig']->render('template/team_submit.twig', array(
			'form' => $form->createView())
	);
})
->bind('team_submit');

$app->match('/team/activate/{hash}', function (Request $request, $hash) use ($app) {
	$team = TeamQuery::create()
		->filterByHash($hash)
		->findOne();
	if(!$team) {
		$app->abort(404, 'Team not recognized.');
	}
	$team->activate(true);
    $app['session']->getFlashBag()->add(
      'info',
      array(
        'message' => $app['translator']->trans('flash.success.team_activated'),
      )
    );
	return $app->redirect($app['url_generator']->generate('homepage'));
})
->bind('team_activate');


$app->get('/{page}', function ($page) use ($app) {
	try{
		return $app['twig']->render('template/'.$page.'.twig', array(
			'pageName' => $page,
		));
	} catch (Exception $e){
		if('Twig_Error_Loader' == get_class($e)){
			$app->abort(404, 'Twig template does not exist.');
		}else {
			throw $e;
		}
	}
})
->value('page', $app['zilex.index'])
->bind('page');

$app->error(function (\Exception $e, $code) use ($app) {
	if($app['debug']) {
		return;
	}
	switch ($code) {
		case 404:
			return new Response( $app['twig']->render('template/404.twig'), 404);
			break;
		default:
			$message = 'We are sorry, but something went terribly wrong.';
	}

	return new Response($message);
});

return $app;