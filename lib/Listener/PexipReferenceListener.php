<?php

namespace OCA\Pexip\Listener;

use OCA\Pexip\AppInfo\Application;
use OCP\Collaboration\Reference\RenderReferenceEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

/**
 * @template-implements IEventListener<RenderReferenceEvent>
 */
class PexipReferenceListener implements IEventListener {
	public function handle(Event $event): void {
		if (!$event instanceof RenderReferenceEvent) {
			return;
		}

		Util::addScript(Application::APP_ID, Application::APP_ID . '-reference');
	}
}
