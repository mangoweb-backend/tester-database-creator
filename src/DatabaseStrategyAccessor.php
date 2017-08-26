<?php declare(strict_types = 1);

namespace App\Model;

use Mangoweb\Tester\DatabaseCreator\Strategies\IDatabaseCreationStrategy;


interface DatabaseStrategyAccessor
{

	public function get(): IDatabaseCreationStrategy;

}
