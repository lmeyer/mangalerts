<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../src/mangalerts_functions.php';

$app = new Silex\Application();
$env = getenv('APP_ENV') ?: 'prod';
// Services

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../config/$env.json"));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
	'translator.messages' => array(),
));
$app->register(new Propel\Silex\PropelServiceProvider(), array(
	'propel.config_file' => __DIR__ . '/../config/mangalerts-conf.php',
	'propel.model_path'  => __DIR__ . '/../src',
));
$app->register(new Silex\Provider\SessionServiceProvider(array(
		'session.storage.save_path' => sys_get_temp_dir())
));
$app->register(new Knp\Provider\ConsoleServiceProvider(), array(
	'console.name'              => 'Mangalerts',
	'console.version'           => '1.0.0',
	'console.project_directory' => __DIR__.'/..'
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['swiftmailer.options'] = array(
	'host' => $app['mailer.host'],
	'port' => $app['mailer.port']
);

return $app;