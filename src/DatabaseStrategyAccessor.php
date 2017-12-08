<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

use Mangoweb\Tester\DatabaseCreator\Strategies\IDatabaseCreationStrategy;


interface DatabaseStrategyAccessor
{

	public function get(): IDatabaseCreationStrategy;

}
