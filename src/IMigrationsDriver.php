<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

interface IMigrationsDriver
{

	public function getMigrationsHash(): string;


	/**
	 * @throws CannotContinueMigrationException
	 */
	public function continue(): void;


	public function reset(): void;

}
