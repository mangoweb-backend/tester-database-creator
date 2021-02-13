<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Bridges\NextrasDbal;

use DateTime;
use Mangoweb\Tester\DatabaseCreator\IDbal;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Result\Row;

class NextrasDbalAdapter implements IDbal
{

	/** @var Connection */
	private $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	public function query(string $sql): array
	{
		$this->connection->connect();
		return array_map(
			function (Row $row) {
				return $row->toArray();
			},
			iterator_to_array($this->connection->query('%raw', $sql))
		);
	}

	public function exec(string $sql): int
	{
		$this->connection->connect();
		$this->connection->query('%raw', $sql);
		return $this->connection->getAffectedRows();
	}

	public function escapeString(string $value): string
	{
		$this->connection->connect();
		return $this->connection->getDriver()->convertStringToSql($value);
	}

	public function escapeInt(int $value): string
	{
		return (string) $value;
	}

	public function escapeBool(bool $value): string
	{
		$this->connection->connect();
		return $this->connection->getDriver()->convertBoolToSql($value);
	}

	public function escapeDateTime(DateTime $value): string
	{
		$this->connection->connect();
		return $this->connection->getDriver()->convertDateTimeToSql($value);
	}

	public function escapeIdentifier(string $value): string
	{
		$this->connection->connect();
		return $this->connection->getDriver()->convertIdentifierToSql($value);
	}

	public function connectToDatabase(string $name): void
	{
		$this->connection->reconnectWithConfig(['database' => $name]);
	}

}
