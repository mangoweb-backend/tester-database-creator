<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\NetteDI;

use Mangoweb\Tester\DatabaseCreator\Bridges\NetteTester\DatabaseNameResolver;
use Mangoweb\Tester\DatabaseCreator\DatabaseCreator;
use Mangoweb\Tester\DatabaseCreator\DatabaseStrategyAccessor;
use Mangoweb\Tester\DatabaseCreator\Drivers\MySqlDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\Drivers\PostgreSqlDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\IDbal;
use Mangoweb\Tester\DatabaseCreator\Mutex;
use Mangoweb\Tester\DatabaseCreator\Strategies\ContinueOrResetDatabaseStrategy;
use Mangoweb\Tester\DatabaseCreator\Strategies\ResetDatabaseStrategy;
use Mangoweb\Tester\DatabaseCreator\Strategies\TemplateDatabaseStrategy;
use Nette\DI\CompilerExtension;


class DatabaseCreatorExtension extends CompilerExtension
{
	public $defaults = [
		'testDatabaseFormat' => DatabaseNameResolver::DEFAULT_FORMAT,
		'dbal' => NULL,
		'migrations' => NULL,
		'driver' => NULL,
		'strategy' => NULL,
	];


	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);

		assert($config['dbal'] !== NULL);
		assert($config['migrations'] !== NULL);
		assert($config['driver'] !== NULL);
		assert($config['strategy'] !== NULL);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('databaseNameResolver'))
			->setClass(DatabaseNameResolver::class)
			->setArguments([$config['testDatabaseFormat']]);
		$builder->addDefinition($this->prefix('mutex'))
			->setClass(Mutex::class)
			->setArguments([$builder->expand('%tempDir%')]);
		$builder->addDefinition($this->prefix('databaseCreator'))
			->setClass(DatabaseCreator::class);

		$this->registerDbal($config['dbal']);
		$this->registerMigrations($config['migrations']);
		$this->registerDriver($config['driver']);
		$this->registerStrategy($config['strategy']);
	}


	private function registerDbal($dbal): void
	{
		$builder = $this->getContainerBuilder();
		$def = $builder->addDefinition($this->prefix('dbal'));
		$def->setClass(IDbal::class);
		$def->setFactory($dbal);
	}


	private function registerMigrations($migrations): void
	{
		$builder = $this->getContainerBuilder();
		$def = $builder->addDefinition($this->prefix('migrationsDriver'));
		$def->setFactory($migrations);
	}


	private function registerDriver($driver): void
	{
		$builder = $this->getContainerBuilder();
		$def = $builder->addDefinition($this->prefix('databaseDriver'));

		if ($driver === 'postgres') {
			$def->setFactory(PostgreSqlDatabaseDriver::class);
		} elseif ($driver === 'mysql') {
			$def->setFactory(MySqlDatabaseDriver::class);
		}
	}


	private function registerStrategy($strategy): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('databaseStrategyAccessor'))
			->setImplement(DatabaseStrategyAccessor::class)
			->setFactory($this->prefix('@strategy'));

		$def = $builder->addDefinition($this->prefix('strategy'));
		if ($strategy === 'template') {
			$def->setFactory(TemplateDatabaseStrategy::class, [TemplateDatabaseStrategy::DEFAULT_FORMAT]);
		} elseif ($strategy === 'reset') {
			$def->setFactory(ResetDatabaseStrategy::class);
		} elseif ($strategy === 'continueOrReset') {
			$def->setFactory(ContinueOrResetDatabaseStrategy::class);
		} else {
			$def->setFactory($strategy);
		}
	}

}
