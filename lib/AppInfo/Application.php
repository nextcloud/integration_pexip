<?php
/**
 * Nextcloud - Pexip
 *
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2023
 */

namespace OCA\Pexip\AppInfo;

use OCA\Pexip\Listener\PexipReferenceListener;
use OCA\Pexip\Reference\PexipReferenceProvider;
use OCP\Collaboration\Reference\RenderReferenceEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\IAppConfig;

class Application extends App implements IBootstrap {

	public const APP_ID = 'integration_pexip';
	public const MAX_CALL_IDLE_TIME = 60 * 60 * 24 * 10;

	private IAppConfig $appConfig;

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();
		/** @var IAppConfig appConfig */
		$this->appConfig = $container->get(IAppConfig::class);
	}

	public function register(IRegistrationContext $context): void {
		$pexipUrl = $this->appConfig->getValueString(self::APP_ID, 'pexip_url');
		if ($pexipUrl !== '') {
			$context->registerReferenceProvider(PexipReferenceProvider::class);
			$context->registerEventListener(RenderReferenceEvent::class, PexipReferenceListener::class);
		}
	}

	public function boot(IBootContext $context): void {
	}
}

