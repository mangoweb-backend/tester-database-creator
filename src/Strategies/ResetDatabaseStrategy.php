<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Strategies;

use Mangoweb\Tester\DatabaseCreator\Drivers\IDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\IMigrationsDriver;


class ResetDatabaseStrategy implements IDatabaseCreationStrategy
{

	/** @var IDatabaseDriver */
	private $databaseDriver;

	/** @var IMigrationsDriver */
	private $migrationsDriver;


	public function __construct(IDatabaseDriver $databaseDriver, IMigrationsDriver $migrationsDriver)
	{
		$this->databaseDriver = $databaseDriver;
		$this->migrationsDriver = $migrationsDriver;
	}


	public function prepareDatabase(string $name): void
	{
		$this->databaseDriver->connectToDatabase($name);
		$this->migrationsDriver->reset();
	}

}
