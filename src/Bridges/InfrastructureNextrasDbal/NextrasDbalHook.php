<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\InfrastructureNextrasDbal;

use Mangoweb\Tester\Infrastructure\Container\AppContainerHook;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nextras\Dbal\Connection;

class NextrasDbalHook extends AppContainerHook
{

	public function onCompile(ContainerBuilder $builder): void
	{
		$def = $builder->getDefinitionByType(Connection::class);
		assert($def instanceof ServiceDefinition);
		NextrasDbalServiceHelpers::modifyConnectionDefinition($def);
	}
}
