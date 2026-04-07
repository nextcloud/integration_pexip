<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Pexip\Db;

use OCP\AppFramework\Db\Entity;
use OCP\DB\Types;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getPexipId()
 * @method void setPexipId(string $pexipId)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getPin()
 * @method void setPin(string $pin)
 * @method string getGuestPin()
 * @method void setGuestPin(string $guestPin)
 * @method bool getGuestsCanPresent()
 * @method void setGuestsCanPresent(bool $guestsCanPresent)
 * @method bool getAllowGuests()
 * @method void setAllowGuests(bool $allowGuests)
 * @method int getLastUsedTimestamp()
 * @method void setLastUsedTimestamp(int $lastUsedTimestamp)
 */
class Call extends Entity implements \JsonSerializable {

	/** @var string */
	protected $userId;
	/** @var string */
	protected $pexipId;
	/** @var string */
	protected $description;
	/** @var string */
	protected $pin;
	/** @var string */
	protected $guestPin;
	/** @var bool */
	protected $guestsCanPresent;
	/** @var bool */
	protected $allowGuests;
	/** @var int */
	protected $lastUsedTimestamp;

	public function __construct() {
		$this->addType('userId', Types::STRING);
		$this->addType('pexipId', Types::STRING);
		$this->addType('description', Types::STRING);
		$this->addType('pin', Types::STRING);
		$this->addType('guestPin', Types::STRING);
		$this->addType('guestsCanPresent', Types::BOOLEAN);
		$this->addType('allowGuests', Types::BOOLEAN);
		$this->addType('lastUsedTimestamp', Types::INTEGER);
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'id' => $this->getId(),
			'user_id' => $this->getUserId(),
			'pexip_id' => $this->getPexipId(),
			'description' => $this->getDescription(),
			'pin' => $this->getPin(),
			'guest_pin' => $this->getGuestPin(),
			'guests_can_present' => $this->getGuestsCanPresent(),
			'allow_guests' => $this->getAllowGuests(),
			'last_used_timestamp' => $this->getLastUsedTimestamp(),
		];
	}
}
