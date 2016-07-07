<?php


namespace PhpMyAdminBundle\Repository;

class TablesRepository extends GenericRepository
{
    public function getTables()
    {
        return $this->getConnection()->getSchemaManager()->listTables();
    }

    /**
     * @param string $table_name
     */
    public function dropTable($table_name) {
        $this->getConnection()->getSchemaManager()->dropTable($table_name);
    }

    /**
     * @param string $table_name
     */
    public function createTable($table_name) {
        $schema = $this->getConnection()->getSchemaManager()->createSchema();

        if(!$schema->hasTable($table_name)) {

            $table = $schema->createTable($table_name);

            $table->addColumn('id', 'integer', ['unsigned' => true]);

            $this->getConnection()->getSchemaManager()->createTable($table);

        }

    }

    /**
     * {@inheritdoc}
     */
    public function getTable() {
        return 'schema_data';
    }

}
