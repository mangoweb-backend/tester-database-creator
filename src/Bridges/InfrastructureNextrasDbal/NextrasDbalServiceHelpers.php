<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\InfrastructureNextrasDbal;

use Mangoweb\Tester\DatabaseCreator\DatabaseCreator;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\StaticClass;

class NextrasDbalServiceHelpers
{

	use StaticClass;

	public static function modifyConnectionDefinition(ServiceDefinition $definition): void
	{
		$factory = $definition->getFactory();
		assert($factory !== null);
		$args = $factory->arguments;
		$args['config'] = new Statement('array_merge(?, ?)', [
			$args['config'],
			[
				'database' => new Statement('@' . DatabaseCreator::class . '::getDatabaseName'),
			],
		]);
		$definition->setArguments($args);
		$definition->addSetup(['@' . DatabaseCreator::class, 'createTestDatabase']);
	}
}
