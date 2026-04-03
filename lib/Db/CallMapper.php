<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2023, Julien Veyssier <julien-nc@posteo.net>
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Pexip\Db;

use DateTime;
use OCA\Pexip\AppInfo\Application;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCP\AppFramework\Db\DoesNotExistException;

/**
 * @extends QBMapper<Call>
 */
class CallMapper extends QBMapper {

	public function __construct(IDBConnection  $db) {
		parent::__construct($db, 'pexip_call', Call::class);
	}

	/**
	 * @param int $id
	 * @return Call
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getCall(int $id): Call {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $pexipId
	 * @return Call
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getCallFromPexipId(string $pexipId): Call {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('pexip_id', $qb->createNamedParameter($pexipId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @param string $pexipId
	 * @return Call
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getUserCallFromPexipId(string $userId, string $pexipId): Call {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('pexip_id', $qb->createNamedParameter($pexipId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return array
	 * @throws Exception
	 */
	public function getUserCalls(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @param string $userId
	 * @param string $pexipId
	 * @param string $description
	 * @param string $pin
	 * @param string $guestPin
	 * @param bool $guestsCanPresent
	 * @param bool $allowGuests
	 * @param int|null $lastUsedTimestamp
	 * @return Call
	 * @throws Exception
	 */
	public function createCall(string $userId, string $pexipId, string $description, string $pin, string $guestPin,
							   bool $guestsCanPresent, bool $allowGuests, ?int $lastUsedTimestamp = null): Call {
		$call = new Call();
		$call->setUserId($userId);
		$call->setPexipId($pexipId);
		$call->setDescription($description);
		$call->setPin($pin);
		$call->setGuestPin($guestPin);
		if ($guestsCanPresent) {
			$call->setGuestsCanPresent($guestsCanPresent);
		}
		if ($allowGuests) {
			$call->setAllowGuests($allowGuests);
		}
		if ($lastUsedTimestamp === null) {
			$lastUsedTimestamp = (new DateTime())->getTimestamp();
		}
		$call->setLastUsedTimestamp($lastUsedTimestamp);
		return $this->insert($call);
	}

	/**
	 * @param int $id
	 * @return mixed|Entity
	 * @throws Exception
	 */
	public function touchCall(int $id) {
		try {
			$call = $this->getCall($id);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		$ts = (new DateTime())->getTimestamp();
		$call->setLastUsedTimestamp($ts);
		return $this->update($call);
	}

	/**
	 * @param int $id
	 * @return Call|null
	 * @throws Exception
	 */
	public function deleteCall(int $id): ?Call {
		try {
			$call = $this->getCall($id);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		return $this->delete($call);
	}

	/**
	 * @param string $userId
	 * @param string $pexipId
	 * @return Call|null
	 * @throws Exception
	 */
	public function deleteUserCallFromPexipId(string $userId, string $pexipId): ?Call {
		try {
			$call = $this->getUserCallFromPexipId($userId, $pexipId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		return $this->delete($call);
	}

	/**
	 * @param int $maxAge
	 * @return int
	 * @throws Exception
	 */
	public function cleanupCalls(int $maxAge = Application::MAX_CALL_IDLE_TIME): int {
		$ts = (new DateTime())->getTimestamp();
		$maxTimestamp = $ts - $maxAge;

		$qb = $this->db->getQueryBuilder();

		// delete generations
		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->lt('last_used_timestamp', $qb->createNamedParameter($maxTimestamp, IQueryBuilder::PARAM_INT))
			);
		return $qb->executeStatement();
	}
}
