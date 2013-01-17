<?php



/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.mangalerts
 */
class User extends BaseUser
{
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew()) {
			do {
				$code = MangalertsFunctions::generateKey(20);
				$hash = MangalertsFunctions::generateKey(30);

				$duplicate = UserQuery::create()
					->filterByCode($code)
					->_or()
					->filterByHash($hash)
					->find();

			} while (0 != count($duplicate));

			$this->setCode($code);
			$this->setHash($hash);
		}
		parent::save($con);
	}

	public function activate($save) {
		$this->setStatus(1);
		if ($save) {
			$this->save();
		}
	}
}
