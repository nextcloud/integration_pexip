<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Cron;

use OCA\Pexip\Db\CallMapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use Psr\Log\LoggerInterface;

class CleanupCalls extends TimedJob {

	public function __construct(
		ITimeFactory $time,
		private CallMapper $callMapper,
		private LoggerInterface $logger,
	) {
		parent::__construct($time);
		$this->setInterval(60 * 60 * 24);
	}

	protected function run($argument) {
		$this->logger->debug('Run cleanup job for Pexip calls');
		$cleanedUp = $this->callMapper->cleanupCalls();
		$this->logger->debug('Deleted ' . $cleanedUp . ' idle calls');
	}
}
