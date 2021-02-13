<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Drivers;

use Mangoweb\Tester\DatabaseCreator\IDbal;

class PostgreSqlDatabaseDriver implements IDatabaseDriver, ITemplateDatabaseDriver
{

	/** @var IDbal */
	private $dbal;

	public function __construct(IDbal $dbal)
	{
		$this->dbal = $dbal;
	}

	public function getDatabaseName(): string
	{
		$result = $this->dbal->query('SELECT current_database();');
		return reset($result[0]);
	}

	public function hasTemplateDatabase(string $name): bool
	{
		$statement = $this->dbal->query(
			'SELECT Count(datname) FROM pg_database WHERE datistemplate = false AND datname = ' . $this->dbal->escapeString($name)
		);
		return $statement[0]['count'] === 1;
	}

	public function createTemplateDatabase(string $name): void
	{
		$this->dbal->query(sprintf('CREATE DATABASE %s WITH TEMPLATE template1', $this->dbal->escapeIdentifier($name)));
	}

	public function createDatabaseFromTemplate(string $templateDb, string $dbName): void
	{
		$dbName = $this->dbal->escapeIdentifier($dbName);
		$templateDb = $this->dbal->escapeIdentifier($templateDb);

		$this->dbal->query(sprintf('DROP DATABASE IF EXISTS %s', $dbName));
		$this->dbal->query(sprintf('CREATE DATABASE %s WITH TEMPLATE %s', $dbName, $templateDb));
	}

	public function connectToDatabase(string $name): void
	{
		$this->dbal->connectToDatabase($name);
	}

}
