<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Database;

use Nette,
	PDO,
	Nette\ObjectMixin;


/**
 * Represents a prepared statement / result set.
 *
 * @author     David Grudl
 *
 * @property-read Connection $connection
 * @property-write $fetchMode
 */
class Statement extends \PDOStatement
{
	/** @var Connection */
	private $connection;

	/** @var float */
	private $time;

	/** @var array */
	private $types;


	protected function __construct(Connection $connection)
	{
		$this->connection = $connection;
		$this->setFetchMode(PDO::FETCH_CLASS, 'Nette\Database\Row', array($this));
	}


	/**
	 * @return Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}


	/**
	 * @return string
	 */
	public function getQueryString()
	{
		return $this->queryString;
	}


	/**
	 * @return int
	 */
	public function getColumnCount()
	{
		return $this->columnCount();
	}


	/**
	 * @return int
	 */
	public function getRowCount()
	{
		return $this->rowCount();
	}


	/**
	 * Executes statement.
	 * @param  array
	 * @return self
	 */
	public function execute($params = array())
	{
		static $types = array('boolean' => PDO::PARAM_BOOL, 'integer' => PDO::PARAM_INT,
			'resource' => PDO::PARAM_LOB, 'NULL' => PDO::PARAM_NULL);

		foreach ($params as $key => $value) {
			$type = gettype($value);
			$this->bindValue(is_int($key) ? $key + 1 : $key, $value, isset($types[$type]) ? $types[$type] : PDO::PARAM_STR);
		}

		$time = microtime(TRUE);
		try {
			parent::execute();
		} catch (\PDOException $e) {
			$e->queryString = $this->queryString;
			throw $e;
		}
		$this->time = microtime(TRUE) - $time;
		$this->connection->__call('onQuery', array($this, $params)); // $this->connection->onQuery() in PHP 5.3

		return $this;
	}


	/**
	 * Fetches into an array where the 1st column is a key and all subsequent columns are values.
	 * @return array
	 */
	public function fetchPairs()
	{
		return $this->fetchAll(PDO::FETCH_KEY_PAIR); // since PHP 5.2.3
	}


	/**
	 * Fetches single field.
	 * @return mixed|FALSE
	 */
	public function fetchField($column = 0)
	{
		$row = $this->fetch();
		return $row ? $row[$column] : FALSE;
	}


	/**
	 * Normalizes result row.
	 * @param  array
	 * @return array
	 */
	public function normalizeRow($row)
	{
		foreach ($this->detectColumnTypes() as $key => $type) {
			$value = $row[$key];
			if ($value === NULL || $value === FALSE || $type === IReflection::FIELD_TEXT) {

			} elseif ($type === IReflection::FIELD_INTEGER) {
				$row[$key] = is_float($tmp = $value * 1) ? $value : $tmp;

			} elseif ($type === IReflection::FIELD_FLOAT) {
				$value = strpos($value, '.') === FALSE ? $value : rtrim(rtrim($value, '0'), '.');
				$float = (float) $value;
				$row[$key] = (string) $float === $value ? $float : $value;

			} elseif ($type === IReflection::FIELD_BOOL) {
				$row[$key] = ((bool) $value) && $value !== 'f' && $value !== 'F';

			} elseif ($type === IReflection::FIELD_DATETIME || $type === IReflection::FIELD_DATE || $type === IReflection::FIELD_TIME) {
				$row[$key] = new Nette\DateTime($value);

			}
		}

		return $this->connection->getSupplementalDriver()->normalizeRow($row, $this);
	}


	private function detectColumnTypes()
	{
		if ($this->types === NULL) {
			$this->types = array();
			if ($this->connection->getSupplementalDriver()->isSupported(ISupplementalDriver::SUPPORT_COLUMNS_META)) { // workaround for PHP bugs #53782, #54695
				$count = $this->columnCount();
				for ($col = 0; $col < $count; $col++) {
					$meta = $this->getColumnMeta($col);
					if (isset($meta['native_type'])) {
						$this->types[$meta['name']] = Helpers::detectType($meta['native_type']);
					}
				}
			}
		}
		return $this->types;
	}


	/**
	 * @return float
	 */
	public function getTime()
	{
		return $this->time;
	}


	/********************* misc tools ****************d*g**/


	/**
	 * Displays complete result set as HTML table for debug purposes.
	 * @return void
	 */
	public function dump()
	{
		Helpers::dumpResult($this);
	}


	/********************* Nette\Object behaviour ****************d*g**/


	/**
	 * @return Nette\Reflection\ClassType
	 */
	public static function getReflection()
	{
		return new Nette\Reflection\ClassType(get_called_class());
	}


	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}


	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}


	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}


	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}


	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}

}
