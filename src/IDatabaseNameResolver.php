<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

interface IDatabaseNameResolver
{

	public function getDatabaseName(): string;

}
