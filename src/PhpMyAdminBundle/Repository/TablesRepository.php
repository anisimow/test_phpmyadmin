<?php


namespace PhpMyAdminBundle\Repository;


use Knp\Component\Pager\Paginator;

class TablesRepository extends GenericRepository
{
    public function getTables()
    {
        return $this->getConnection()->getSchemaManager()->listTables();
    }

    /**
     * @param string $table_name
     */
    public function dropTable($table_name)
    {
        $this->getConnection()->getSchemaManager()->dropTable($table_name);
    }

    /**
     * @param string $table_name
     * @return boolean
     */
    public function createTable($table_name)
    {
        $result = FALSE;

        $schema = $this->getConnection()->getSchemaManager()->createSchema();

        if(!$schema->hasTable($table_name)) {

            $table = $schema->createTable($table_name);

            $table->addColumn('id', 'integer', ['unsigned' => true]);

            $this->getConnection()->getSchemaManager()->createTable($table);

            $result = TRUE;
        }

        return $result;
    }

    /**
     * @param $table_name
     * @return \Doctrine\DBAL\Schema\Column[]
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function getTableColumns($table_name)
    {
        $schema = $this->getConnection()->getSchemaManager()->createSchema();

        return $schema->getTable($table_name)->getColumns();
    }

    /**
     * @param $table_name
     * @param $page
     * @param $limit
     * @return Paginator
     */
    public function getTablePaginator($table_name, $page, $limit)
    {
        $query = $this->getConnection()
            ->createQueryBuilder()
            ->select('*')
            ->from($table_name);

        $query->execute();

        return $this->getPaginator()->paginate($query, $page, $limit);
    }

    public function deleteTableRow($table_name, $id)
    {
        $query = $this->getConnection()
            ->createQueryBuilder()
            ->delete($table_name)
            ->where('id = :id')
            ->setParameter(':id', $id);

        return $query -> execute();
    }

    public function insertTableRow($table_name, array $values)
    {
        $insert_values = $parameters = array();

        foreach ($values as $key => $val) {
            $parameters[':'.$key] = $val;
            $insert_values[$key] = ':'.$key;
        }

        $query = $this->getConnection()
            ->createQueryBuilder()
            ->insert($table_name)
            ->values($insert_values)
            ->setParameters($parameters);

        return $query -> execute();
    }

    public function getRawById($table_name, $id)
    {
        $query = $this->getConnection()
            ->createQueryBuilder()
            ->select('*')
            ->where('id=:id')
            ->from($table_name)
            ->setParameter(':id', $id);

        return $query->execute()->fetch();
    }

    public function updateTableRow($table_name, $id, $values)
    {
        $insert_values = $parameters = array();

        foreach ($values as $key => $val) {
            $parameters[':'.$key] = $val;
            $insert_values[$key] = ':'.$key;
        }

        $query = $this->getConnection()
            ->createQueryBuilder()
            ->update($table_name)
            ->where('id=:id')
            ->setParameters(
                array_merge(
                    $parameters,
                    array(':id' => $id)
                )
            );

        foreach($insert_values as $key => $value) {
            $query ->set($key, $value);
        }

        return $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getTable() {
        return 'schema_data';
    }

}
