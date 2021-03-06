<?php



/**
 * This class defines the structure of the 'user' table.
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
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'mangalerts.map.UserTableMap';

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
        $this->setName('user');
        $this->setPhpName('User');
        $this->setClassname('User');
        $this->setPackage('mangalerts');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 255, null);
        $this->addColumn('code', 'Code', 'VARCHAR', true, 255, null);
        $this->addColumn('hash', 'Hash', 'VARCHAR', true, 255, null);
        $this->addColumn('status', 'Status', 'INTEGER', true, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserTeam', 'UserTeam', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'UserTeams');
        $this->addRelation('Team', 'Team', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Teams');
    } // buildRelations()

} // UserTableMap
