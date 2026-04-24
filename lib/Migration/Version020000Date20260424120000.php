<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version020000Date20260424120000 extends SimpleMigrationStep {


	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$table = $schema->getTable('pexip_call');
		$column = $table->getColumn('pexip_id');
		if ($column->getLength() > 750) {
			$column->setLength(750);
		}

		$column = $table->getColumn('description');
		if ($column->getNotnull()) {
			$column->setNotnull(false);
		}

		$table->dropIndex('pexip_call_pexip_id');
		$table->addUniqueIndex(['pexip_id'], 'pexip_call_uniqpexip_id');

		return $schema;
	}

}
