<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

use Mangoweb\ExceptionResponsibility\ResponsibilityApp;


class CannotContinueMigrationException extends \RuntimeException implements ResponsibilityApp
{

}
