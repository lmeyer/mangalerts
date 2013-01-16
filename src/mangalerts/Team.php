<?php



/**
 * Skeleton subclass for representing a row from the 'team' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.mangalerts
 */
class Team extends BaseTeam
{
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew()) {
			do {
				$hash = MangalertsFunctions::generateKey(30);

				$duplicate = TeamQuery::create()
					->filterByHash($hash)
					->find();

			} while (0 != count($duplicate));

			$this->setHash($hash);
			$this->setLastCheck(time());
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
