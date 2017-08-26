<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Strategies;

use Mangoweb\Tester\DatabaseCreator\Drivers\ITemplateDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\IMigrationsDriver;
use Mangoweb\Tester\DatabaseCreator\Mutex;


class TemplateDatabaseStrategy implements IDatabaseCreationStrategy
{
	public const DEFAULT_FORMAT = 'app_template_%s';

	/** @var string */
	private $templateDbFormat;

	/** @var ITemplateDatabaseDriver */
	private $driver;

	/** @var IMigrationsDriver */
	private $migrationsDriver;

	/** @var Mutex */
	private $mutex;


	public function __construct(string $templateDbFormat, Mutex $mutex, ITemplateDatabaseDriver $driver, IMigrationsDriver $migrationsDriver)
	{
		$this->templateDbFormat = $templateDbFormat;
		$this->driver = $driver;
		$this->migrationsDriver = $migrationsDriver;
		$this->mutex = $mutex;
	}


	public function prepareDatabase(string $name): void
	{
		$templateDbName = sprintf($this->templateDbFormat, $this->migrationsDriver->getMigrationsHash());
		$this->mutex->synchronized(__METHOD__, function () use ($templateDbName) {
			if (!$this->driver->hasTemplateDatabase($templateDbName)) {
				$oldDatabaseName = $this->driver->getDatabaseName();
				$this->driver->createTemplateDatabase($templateDbName);
				$this->driver->connectToDatabase($templateDbName);
				$this->migrationsDriver->reset();
				$this->driver->connectToDatabase($oldDatabaseName);
			}
		});
		$this->driver->createDatabaseFromTemplate($templateDbName, $name);
	}

}
