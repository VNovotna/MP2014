<?php

namespace DB;

use Nette;

/**
 * Abstract class performing all actions in the database
 * @author viky
 */
abstract class Repository extends Nette\Object {

    /** @var Nette\Database\Connection */
    private $database;

    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
    }

    /**
     * @param string $tableName
     * @return Nette\Database\Table\Selection
     */
    protected function getTable($tableName = '') {
        if ($tableName == '') {
            // table name from class name
            preg_match('#(\w+)Repository$#', get_class($this), $m);
            $tableName = lcfirst($m[1]);
        }
        return $this->connection->table($tableName);
    }

    /**
     * Returns all rows from table
     * @return Nette\Database\Table\Selection
     */
    public function findAll() {
        return $this->getTable();
    }

    /**
     * return table rows based on the filter, eg: array('name' => 'John').
     * @return Nette\Database\Table\Selection
     */
    public function findBy(array $by) {
        return $this->getTable()->where($by);
    }

}
