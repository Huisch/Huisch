<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Telegram;

class HuischTelegram extends Telegram {
	private $em;

	public function __construct($api_key, $bot_username = '', EntityManagerInterface $em = null) {
		parent::__construct($api_key, $bot_username);
		$this->em = $em;
	}

	public function getCommandObject($command) {
		$command_namespace = __NAMESPACE__ . '\\TelegramCommands\\' . $this->ucfirstUnicode($command) . 'Command';
		if (class_exists($command_namespace)) {
			return new $command_namespace($this, $this->update, $this->em);
		}
		return parent::getCommandObject($command);
	}
}
