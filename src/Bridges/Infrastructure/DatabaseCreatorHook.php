<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\Infrastructure;

use Mangoweb\Tester\DatabaseCreator\DatabaseCreator;
use Mangoweb\Tester\Infrastructure\Container\AppContainerHook;
use Nette\DI\Container;
use Nette\DI\ContainerBuilder;


class DatabaseCreatorHook extends AppContainerHook
{
	/** @var DatabaseCreator */
	private $databaseCreator;


	public function __construct(DatabaseCreator $databaseCreator)
	{
		$this->databaseCreator = $databaseCreator;
	}


	public function onCompile(ContainerBuilder $builder): void
	{
		if (class_exists(\Nette\DI\Definitions\ImportedDefinition::class)) {
			$builder->addImportedDefinition('databaseCreator')
				->setClass(DatabaseCreator::class);
		} else {
			$builder->addDefinition('databaseCreator')
				->setClass(DatabaseCreator::class)
				->setDynamic(true);
		}
		if (method_exists($builder, 'resolve')) {
			$builder->resolve();
		} else {
			$builder->prepareClassList();
		}
	}


	public function onCreate(Container $applicationContainer): void
	{
		$applicationContainer->addService('databaseCreator', $this->databaseCreator);
	}
}
