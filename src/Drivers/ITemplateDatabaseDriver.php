<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator\Drivers;

interface ITemplateDatabaseDriver extends IDatabaseDriver
{

	public function hasTemplateDatabase(string $name): bool;

	public function createTemplateDatabase(string $name): void;

	public function createDatabaseFromTemplate(string $templateDb, string $dbName): void;

}
