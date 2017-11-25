<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Drivers;

use Mangoweb\Tester\DatabaseCreator\IDbal;


class MySqlDatabaseDriver implements IDatabaseDriver
{

	/** @var IDbal */
	private $dbal;


	public function __construct(IDbal $dbal)
	{
		$this->dbal = $dbal;
	}


	public function getDatabaseName(): string
	{
		$result = $this->dbal->query('SELECT DATABASE()');
		return reset($result[0]);
	}


	public function connectToDatabase(string $name): void
	{
		$this->dbal->exec(sprintf(
			'CREATE DATABASE IF NOT EXISTS %s',
			$this->dbal->escapeIdentifier($name)
		));

		$this->dbal->connectToDatabase($name);
	}

}
