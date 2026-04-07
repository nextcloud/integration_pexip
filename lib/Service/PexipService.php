<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Service;

use DateTime;
use Exception;
use OCA\Pexip\AppInfo\Application;
use OCA\Pexip\Db\Call;
use OCA\Pexip\Db\CallMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Collaboration\Reference\IReferenceManager;
use OCP\IAppConfig;
use Throwable;

class PexipService {

	public function __construct(
		private IAppConfig $appConfig,
		private IReferenceManager $referenceManager,
		private CallMapper $callMapper,
	) {
	}

	/**
	 * @param string $pexipId
	 * @return array|string[]
	 * @throws MultipleObjectsReturnedException
	 * @throws \OCP\DB\Exception
	 */
	public function checkCall(string $pexipId): array {
		try {
			$call = $this->callMapper->getCallFromPexipId($pexipId);
			$this->callMapper->touchCall($call->getId());
			$allowGuests = (bool)$call->getAllowGuests();
			$params = [
				'status' => 'success',
				'action' => 'continue',
				'result' => [
					'service_type' => 'conference',
					'name' => $call->getPexipId(),
					'service_tag' => 'Nextcloud',
					'allow_guests' => $allowGuests,
					// We choose the layout
					'view' => 'five_mains_seven_pips',
					//'locked' => false
				],
			];
			if ($call->getDescription()) {
				$params['result']['description'] = $call->getDescription();
			}
			if ($call->getPin()) {
				$params['result']['pin'] = $call->getPin();
			}
			if ($allowGuests) {
				if ($call->getGuestPin()) {
					$params['result']['guest_pin'] = $call->getGuestPin();
				}
				$params['result']['guests_can_present'] = (bool)$call->getGuestsCanPresent();
			}
			return $params;
		} catch (DoesNotExistException $e) {
			return [
				'status' => 'fail',
				'action' => 'continue',
				'result' => [],
			];
		}
	}

	/**
	 * @param string $userId
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function getUserCalls(string $userId): array {
		$pexipUrl = $this->appConfig->getValueString(Application::APP_ID, 'pexip_url');
		return array_map(function (Call $call) use ($pexipUrl) {
			$callArray = $call->jsonSerialize();
			$callArray['link'] = $this->getCallLink($pexipUrl, $call->getPexipId());
			return $callArray;
		}, $this->callMapper->getUserCalls($userId));
	}

	/**
	 * @param string $pexipUrl
	 * @param string $pexipId
	 * @return string
	 */
	private function getCallLink(string $pexipUrl, string $pexipId): string {
		return trim($pexipUrl, " \n\r\t\v\x00/") . '/webapp3/m/' . $pexipId . '/express';
	}

	/**
	 * @param string $userId
	 * @param string $description
	 * @param string $pin
	 * @param string $guestPin
	 * @param bool $guestsCanPresent
	 * @param bool $allowGuests
	 * @return array
	 */
	public function createCall(string $userId, string $description, string $pin = '', string $guestPin = '',
		bool $guestsCanPresent = true, bool $allowGuests = true): array {
		$ts = (new DateTime())->getTimestamp();
		$pexipId = md5($description . $userId . $ts);
		try {
			$call = $this->callMapper->createCall(
				$userId, $pexipId, $description, $pin, $guestPin,
				$guestsCanPresent, $allowGuests
			);
			$callArray = $call->jsonSerialize();
			$pexipUrl = $this->appConfig->getValueString(Application::APP_ID, 'pexip_url');
			$callArray['link'] = $this->getCallLink($pexipUrl, $call->getPexipId());
			return $callArray;
		} catch (Exception|Throwable $e) {
			return [
				'error' => $e->getMessage(),
			];
		}
	}

	/**
	 * @param string $userId
	 * @param string $pexipId
	 * @return bool
	 * @throws \OCP\DB\Exception
	 */
	public function deleteCall(string $userId, string $pexipId): bool {
		$deletedCall = $this->callMapper->deleteUserCallFromPexipId($userId, $pexipId);
		if ($deletedCall !== null) {
			$this->referenceManager->invalidateCache('pexip', $pexipId);
		}
		return $deletedCall !== null;
	}

	/**
	 * @param string $pexipId
	 * @return string[]
	 * @throws MultipleObjectsReturnedException
	 * @throws \OCP\DB\Exception
	 */
	public function getPexipCallInfo(string $pexipId): array {
		try {
			$call = $this->callMapper->getCallFromPexipId($pexipId);
			$this->callMapper->touchCall($call->getId());
			$callArray = $call->jsonSerialize();
			$pexipUrl = $this->appConfig->getValueString(Application::APP_ID, 'pexip_url');
			$callArray['link'] = $this->getCallLink($pexipUrl, $call->getPexipId());
			return $callArray;
		} catch (DoesNotExistException $e) {
			return [
				'error' => 'not found',
			];
		}
	}
}
