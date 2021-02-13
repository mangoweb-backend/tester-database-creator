<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

class MigrationHashSuffixDatabaseNameResolver implements IDatabaseNameResolver
{

	/** @var IDatabaseNameResolver */
	private $nameResolver;

	/** @var IMigrationsDriver */
	private $migrationsDriver;

	public function __construct(IDatabaseNameResolver $nameResolver, IMigrationsDriver $migrationsDriver)
	{
		$this->nameResolver = $nameResolver;
		$this->migrationsDriver = $migrationsDriver;
	}

	public function getDatabaseName(): string
	{
		return $this->nameResolver->getDatabaseName() . '_' . $this->migrationsDriver->getMigrationsHash();
	}
}
