<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\NetteDI;

use Mangoweb\Tester\DatabaseCreator\Bridges\NetteTester\DatabaseNameResolver;
use Mangoweb\Tester\DatabaseCreator\DatabaseCreator;
use Mangoweb\Tester\DatabaseCreator\DatabaseStrategyAccessor;
use Mangoweb\Tester\DatabaseCreator\Drivers\MySqlDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\Drivers\PostgreSqlDatabaseDriver;
use Mangoweb\Tester\DatabaseCreator\IDatabaseNameResolver;
use Mangoweb\Tester\DatabaseCreator\IDbal;
use Mangoweb\Tester\DatabaseCreator\MigrationHashSuffixDatabaseNameResolver;
use Mangoweb\Tester\DatabaseCreator\Mutex;
use Mangoweb\Tester\DatabaseCreator\Strategies\ContinueOrResetDatabaseStrategy;
use Mangoweb\Tester\DatabaseCreator\Strategies\ResetDatabaseStrategy;
use Mangoweb\Tester\DatabaseCreator\Strategies\TemplateDatabaseStrategy;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;


class DatabaseCreatorExtension extends CompilerExtension
{
	public $defaults = [
		'dbal' => null,
		'migrations' => null,
		'driver' => null,
		'strategy' => null,
		'databaseName' => [
			'format' => DatabaseNameResolver::DEFAULT_FORMAT,
			'type' => 'tester',
			'migrationHashSuffix' => false,
		],
	];


	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);

		assert($config['dbal'] !== null);
		assert($config['migrations'] !== null);
		assert($config['driver'] !== null);
		assert($config['strategy'] !== null);

		$builder = $this->getContainerBuilder();

		if (isset($config['testDatabaseFormat'])) {
			trigger_error('testDatabaseFormat is deprecated, use databaseName.format option instead', E_USER_DEPRECATED);
			$config['databaseName']['format'] = $config['testDatabaseFormat'];
		}

		$builder->addDefinition($this->prefix('mutex'))
			->setClass(Mutex::class)
			->setArguments([$builder->parameters['tempDir']]);
		$builder->addDefinition($this->prefix('databaseCreator'))
			->setClass(DatabaseCreator::class);

		$this->registerDbal($config['dbal']);
		$this->registerMigrations($config['migrations']);
		$this->registerDriver($config['driver']);
		$this->registerStrategy($config['strategy']);
		$this->registerNameResolver($config['databaseName']);
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

		if (class_exists(FactoryDefinition::class)) {
			$builder->addAccessorDefinition($this->prefix('databaseStrategyAccessor'))
				->setImplement(DatabaseStrategyAccessor::class)
				->setReference($this->prefix('@strategy'));
		} else {
			$builder->addDefinition($this->prefix('databaseStrategyAccessor'))
				->setImplement(DatabaseStrategyAccessor::class)
				->setFactory($this->prefix('@strategy'));
		}

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


	private function registerNameResolver(array $config): void
	{
		$builder = $this->getContainerBuilder();

		$def = $builder->addDefinition($this->prefix('databaseNameResolver'));
		$def->setClass(IDatabaseNameResolver::class);

		if ($config['type'] === 'tester') {
			$def->setFactory(DatabaseNameResolver::class)
				->setArguments([$config['format']]);
		} else {
			$def->setFactory($config['type']);
		}
		if ($config['migrationHashSuffix'] ?? false) {
			$def->setAutowired(false);
			$builder->addDefinition($this->prefix('databaseNameResolverDecorator'))
				->setClass(IDatabaseNameResolver::class)
				->setFactory(MigrationHashSuffixDatabaseNameResolver::class, [
					'nameResolver' => $def,
				]);
		}
	}

}
