<?php

use Phinx\Migration\AbstractMigration;

class InitDbMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('domains')
            ->addIndex(['id'])
            ->addColumn('name', 'string')
            ->create();

        $this->table('paths')
            ->addIndex(['id'])
            ->addColumn('value', 'string', [ 'limit' => 1829 ])
            ->create();

        $this->table('elements')
            ->addIndex(['id'])
            ->addColumn('name', 'string', [ 'limit' => 20 ])
            ->create();

        $this->table('requests')
            ->addIndex(['id'])
            ->addIndex(['domain_id'])
            ->addIndex(['path_id'])
            ->addIndex(['element_id'])
            ->addColumn('domain_id', 'integer')
            ->addColumn('path_id', 'integer')
            ->addColumn('element_id', 'integer')
            ->addColumn('element_count', 'smallinteger')
            ->addColumn('time', 'datetime')
            ->addColumn('duration', 'smallinteger')
            ->addForeignKey('path_id', 'paths', 'id')
            ->addForeignKey('element_id', 'elements', 'id')
            ->create();
    }
}
