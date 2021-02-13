<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

class DatabaseCreator
{

	/** @var bool */
	private $created = false;

	/** @var IDatabaseNameResolver */
	private $databaseNameResolver;

	/** @var DatabaseStrategyAccessor */
	private $databaseStrategyAccessor;

	public function __construct(DatabaseStrategyAccessor $databaseStrategyAccessor, IDatabaseNameResolver $databaseNameResolver)
	{
		$this->databaseNameResolver = $databaseNameResolver;
		$this->databaseStrategyAccessor = $databaseStrategyAccessor;
	}

	public function getDatabaseName(): string
	{
		return $this->databaseNameResolver->getDatabaseName();
	}

	public function createTestDatabase(): void
	{
		if ($this->created) {
			return;
		}
		$this->databaseStrategyAccessor->get()->prepareDatabase($this->databaseNameResolver->getDatabaseName());
		$this->created = true;
	}

}
