<?php

/**
 * @copyright Copyright (c) 2023 Julien Veyssier <julien-nc@posteo.net>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Pexip\Reference;

use OCA\Pexip\AppInfo\Application;
use OCA\Pexip\Service\PexipService;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Collaboration\Reference\ADiscoverableReferenceProvider;
use OCP\Collaboration\Reference\IReference;
use OCP\Collaboration\Reference\Reference;
use OCP\DB\Exception;
use OCP\IAppConfig;
use OCP\IL10N;

use OCP\IURLGenerator;
use OCP\IUserManager;

class PexipReferenceProvider extends ADiscoverableReferenceProvider {

	private const RICH_OBJECT_TYPE = Application::APP_ID . '_call';

	public function __construct(
		private PexipService $pexipService,
		private IL10N $l10n,
		private IAppConfig $appConfig,
		private IURLGenerator $urlGenerator,
		private IUserManager $userManager,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'pexip-call';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Pexip meetings');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int {
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconUrl(): string {
		return $this->urlGenerator->getAbsoluteURL(
			$this->urlGenerator->imagePath(Application::APP_ID, 'app-dark.svg')
		);
	}

	/**
	 * @inheritDoc
	 */
	public function matchReference(string $referenceText): bool {
		return $this->getPexipId($referenceText) !== null;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveReference(string $referenceText): ?IReference {
		if ($this->matchReference($referenceText)) {
			$pexipId = $this->getPexipId($referenceText);
			if ($pexipId === null) {
				return null;
			}

			try {
				$callInfo = $this->pexipService->getPexipCallInfo($pexipId);
			} catch (MultipleObjectsReturnedException|Exception $e) {
				return null;
			}
			// obfuscate pins
			if ($callInfo['pin']) {
				$callInfo['pin'] = 'xxx';
			}
			if ($callInfo['guest_pin']) {
				$callInfo['guest_pin'] = 'xxx';
			}
			$user = $this->userManager->get($callInfo['user_id']);
			if ($user !== null) {
				$callInfo['user_name'] = $user->getDisplayName();
			}
			$reference = new Reference($referenceText);
			$reference->setRichObject(
				self::RICH_OBJECT_TYPE,
				[
					'call' => $callInfo,
				]
			);
			return $reference;
		}

		return null;
	}

	/**
	 * @param string $url
	 * @return null|string
	 */
	private function getPexipId(string $url): ?string {
		$pexipUrl = $this->appConfig->getValueString(Application::APP_ID, 'pexip_url');
		$this->urlGenerator->getAbsoluteURL('/apps/' . Application::APP_ID);

		// link examples:
		// https://pexip.example/webapp3/m/3jf5wq3hibbqvickir7ysqehfi
		// https://pexip.example/webapp3/m/3jf5wq3hibbqvickir7ysqehfi/express
		preg_match('/^' . preg_quote($pexipUrl, '/') . '\/webapp3\/m\/([0-9a-z]+)(?:\/express)?$/i', $url, $matches);
		if (count($matches) > 1) {
			return $matches[1];
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getCachePrefix(string $referenceId): string {
		return 'pexip';
	}

	/**
	 * @inheritDoc
	 */
	public function getCacheKey(string $referenceId): ?string {
		return $this->getPexipId($referenceId);
	}
}
