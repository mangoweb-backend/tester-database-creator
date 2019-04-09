<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Presets;

use Mangoweb\Tester\Infrastructure\InfrastructureConfigurator;

class NextrasStackPreset
{
	public static function installMysql(InfrastructureConfigurator $configurator)
	{
		$configurator->addConfig(__DIR__ . '/nextras-mysql.neon');
	}


	public static function installPostgresql(InfrastructureConfigurator $configurator)
	{
		$configurator->addConfig(__DIR__ . '/nextras-postgresql.neon');
	}


	public static function installTransactional(InfrastructureConfigurator $configurator)
	{
		$configurator->addConfig(__DIR__ . '/nextras-transactional.neon');
	}
}
