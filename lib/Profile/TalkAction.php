<?php

declare(strict_types=1);

/**
 * @copyright 2021 Christopher Ng <chrng8@gmail.com>
 *
 * @author Christopher Ng <chrng8@gmail.com>
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
 *
 */

namespace OCA\Talk\Profile;

use OCA\Talk\AppInfo\Application;
use OCA\Talk\Config;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\Profile\ILinkAction;

class TalkAction implements ILinkAction {

	/** @var IUser */
	private $targetUser;

	/** @var Config */
	private $config;

	/** @var IFactory */
	private $l10nFactory;

	/** @var IUrlGenerator */
	private $urlGenerator;

	/** @var IUserSession */
	private $userSession;

	public function __construct(
		Config $config,
		IFactory $l10nFactory,
		IURLGenerator $urlGenerator,
		IUserSession $userSession
	) {
		$this->config = $config;
		$this->l10nFactory = $l10nFactory;
		$this->urlGenerator = $urlGenerator;
		$this->userSession = $userSession;
	}

	public function preload(IUser $user): void {
		$this->targetUser = $user;
	}

	public function getAppId(): string {
		return Application::APP_ID;
	}

	public function getId(): string {
		return 'talk';
	}

	public function getDisplayId(): string {
		return $this->l10nFactory->get(Application::APP_ID)->t('Talk');
	}

	public function getTitle(): string {
		$visitingUser = $this->userSession->getUser();
		if (!$visitingUser || $visitingUser === $this->targetUser) {
			return $this->l10nFactory->get(Application::APP_ID)->t('Open Talk');
		}
		return $this->l10nFactory->get(Application::APP_ID)->t('Talk to %s', [$this->targetUser->getDisplayName()]);
	}

	public function getPriority(): int {
		return 10;
	}

	public function getIcon(): string {
		return $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath(Application::APP_ID, 'app-dark.svg'));
	}

	public function getTarget(): ?string {
		$visitingUser = $this->userSession->getUser();
		if (
			$this->config->isDisabledForUser($this->targetUser)
			|| !$visitingUser
			|| $this->config->isDisabledForUser($visitingUser)
		) {
			return null;
		}
		if ($visitingUser === $this->targetUser) {
			return $this->urlGenerator->linkToRouteAbsolute('spreed.Page.index');
		}
		return $this->urlGenerator->linkToRouteAbsolute('spreed.Page.index') . '?callUser=' . $this->targetUser->getUID();
	}
}
