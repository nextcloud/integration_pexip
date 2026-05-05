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

		if (!$schema->hasTable('pexip_call')) {
			return null;
		}
		$table = $schema->getTable('pexip_call');

		$modified = false;
		$column = $table->getColumn('pexip_id');
		if ($column->getLength() > 750) {
			$column->setLength(750);
			$modified = true;
		}

		$column = $table->getColumn('description');
		if ($column->getNotnull()) {
			$column->setNotnull(false);
			$modified = true;
		}

		if ($table->hasIndex('pexip_call_pexip_id')) {
			$table->dropIndex('pexip_call_pexip_id');
			$modified = true;
		}
		if (!$table->hasUniqueConstraint('pexip_call_uniqpexip_id')) {
			$table->addUniqueIndex(['pexip_id'], 'pexip_call_uniqpexip_id');
			$modified = true;
		}

		return $modified ? $schema : null;
	}

}
