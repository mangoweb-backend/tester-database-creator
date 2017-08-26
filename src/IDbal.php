<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;

interface IDbal
{

	/**
	 * @return array list of rows represented by assoc. arrays
	 */
	public function query(string $sql): array;


	/**
	 * @return int number of affected rows
	 */
	public function exec(string $sql): int;


	public function escapeString(string $value): string;


	public function escapeInt(int $value): string;


	public function escapeBool(bool $value): string;


	public function escapeDateTime(\DateTime $value): string;


	public function escapeIdentifier(string $value): string;


	public function connectToDatabase(string $name): void;

}
