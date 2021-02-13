<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Strategies;

interface IDatabaseCreationStrategy
{

	public function prepareDatabase(string $name): void;

}
