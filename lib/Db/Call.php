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

use OCP\AppFramework\Db\Entity;

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
		$this->addType('user_id', 'string');
		$this->addType('pexip_id', 'string');
		$this->addType('description', 'string');
		$this->addType('pin', 'string');
		$this->addType('guest_pin', 'string');
		$this->addType('guests_can_present', 'boolean');
		$this->addType('allow_guests', 'boolean');
		$this->addType('last_used_timestamp', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'pexip_id' => $this->pexipId,
			'description' => $this->description,
			'pin' => $this->pin,
			'guest_pin' => $this->guestPin,
			'guests_can_present' => (bool)$this->guestsCanPresent,
			'allow_guests' => (bool)$this->allowGuests,
			'last_used_timestamp' => $this->lastUsedTimestamp,
		];
	}
}
