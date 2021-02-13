<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\NextrasMigrations;

use Mangoweb\Tester\DatabaseCreator\IDbal;

class MySqlNextrasMigrationsDbalAdapter implements IDbal
{

	/** @var \Nextras\Migrations\IDbal */
	private $migrationsDbal;

	public function __construct(\Nextras\Migrations\IDbal $migrationsDbal)
	{
		$this->migrationsDbal = $migrationsDbal;
	}

	public function query(string $sql): array
	{
		return $this->migrationsDbal->query($sql);
	}

	public function exec(string $sql): int
	{
		return $this->migrationsDbal->exec($sql);
	}

	public function escapeString(string $value): string
	{
		return $this->migrationsDbal->escapeString($value);
	}

	public function escapeInt(int $value): string
	{
		return $this->migrationsDbal->escapeInt($value);
	}

	public function escapeBool(bool $value): string
	{
		return $this->migrationsDbal->escapeBool($value);
	}

	public function escapeDateTime(\DateTime $value): string
	{
		return $this->migrationsDbal->escapeDateTime($value);
	}

	public function escapeIdentifier(string $value): string
	{
		return $this->migrationsDbal->escapeIdentifier($value);
	}

	public function connectToDatabase(string $name): void
	{
		$this->migrationsDbal->exec(sprintf(
			'USE %s',
			$this->escapeIdentifier($name)
		));
	}
}
