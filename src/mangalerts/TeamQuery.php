<?php



/**
 * Skeleton subclass for performing query and update operations on the 'team' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.mangalerts
 */
class TeamQuery extends BaseTeamQuery
{
	public function topten(){
		return $this
			->filterByStatus(1)
			->join('UserTeam', Criteria::LEFT_JOIN)
			->groupById()
			->withColumn("count(*)", "nb_user_by_team")
			->orderBy('nb_user_by_team', criteria::DESC)
			->limit(10);
	}
}
