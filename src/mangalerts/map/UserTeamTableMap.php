<?php



/**
 * This class defines the structure of the 'user_team' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.mangalerts.map
 */
class UserTeamTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'mangalerts.map.UserTeamTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('user_team');
        $this->setPhpName('UserTeam');
        $this->setClassname('UserTeam');
        $this->setPackage('mangalerts');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('user_id', 'UserId', 'INTEGER' , 'user', 'id', true, null, null);
        $this->addForeignPrimaryKey('team_id', 'TeamId', 'INTEGER' , 'team', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Team', 'Team', RelationMap::MANY_TO_ONE, array('team_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // UserTeamTableMap
