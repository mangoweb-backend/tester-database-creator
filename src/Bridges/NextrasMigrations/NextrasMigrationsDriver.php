<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\NextrasMigrations;

use Mangoweb\Tester\DatabaseCreator\CannotContinueMigrationException;
use Mangoweb\Tester\DatabaseCreator\IMigrationsDriver;
use Nextras\Migrations\Engine\Finder;
use Nextras\Migrations\Engine\OrderResolver;
use Nextras\Migrations\Engine\Runner;
use Nextras\Migrations\Entities\Group;
use Nextras\Migrations\Exception;
use Nextras\Migrations\IConfiguration;
use Nextras\Migrations\IDriver;
use Nextras\Migrations\LogicException;
use Nextras\Migrations\Printers\DevNull;


class NextrasMigrationsDriver implements IMigrationsDriver
{

	/** @var Runner */
	private $migrationsRunner;

	/** @var IConfiguration */
	private $configuration;


	public function __construct(IDriver $driver, IConfiguration $configuration)
	{
		$runner = new Runner($driver, new class extends DevNull
		{
			public function printError(Exception $e)
			{
				throw $e;
			}
		});

		foreach ($configuration->getGroups() as $group) {
			$runner->addGroup($group);
		}

		foreach ($configuration->getExtensionHandlers() as $ext => $handler) {
			$runner->addExtensionHandler($ext, $handler);
		}

		$this->migrationsRunner = $runner;
		$this->configuration = $configuration;
	}


	public function reset(): void
	{
		$this->migrationsRunner->run(Runner::MODE_RESET);
	}


	public function continue(): void
	{
		try {
			$this->migrationsRunner->run(Runner::MODE_CONTINUE);
		} catch (LogicException $e) {
			throw new CannotContinueMigrationException($e->getMessage(), 0, $e);
		}
	}


	public function getMigrationsHash(): string
	{
		/** @var Group[] $groups */
		$groups = $this->configuration->getGroups();
		$groups = array_filter($groups, function (Group $group) {
			return $group->name !== 'dummy-data';
		});

		$finder = new Finder();
		$files = $finder->find($groups, ['sql', 'php']);

		$resolver = new OrderResolver();
		$ordered = $resolver->resolve([], $groups, $files, Runner::MODE_CONTINUE);

		return md5(implode('', array_column($ordered, 'checksum')));
	}

}
