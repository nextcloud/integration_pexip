<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010000Date20230326112219 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('pexip_call')) {
			$table = $schema->createTable('pexip_call');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('pexip_id', Types::STRING, [
				'notnull' => true,
				'length' => 1000,
			]);
			$table->addColumn('description', Types::STRING, [
				'notnull' => true,
				'length' => 3000,
			]);
			$table->addColumn('pin', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('guest_pin', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('guests_can_present', Types::BOOLEAN, [
				'notnull' => false,
				'default' => 0,
			]);
			$table->addColumn('allow_guests', Types::BOOLEAN, [
				'notnull' => false,
				'default' => 0,
			]);
			$table->addColumn('last_used_timestamp', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['pexip_id'], 'pexip_call_pexip_id');
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
