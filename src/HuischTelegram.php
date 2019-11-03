<?php

namespace App;

use Longman\TelegramBot\Telegram;

class HuischTelegram extends Telegram {
	public function getCommandObject($command) {
		$command_namespace = __NAMESPACE__ . '\\TelegramCommands\\' . $this->ucfirstUnicode($command) . 'Command';
		if (class_exists($command_namespace)) {
			return new $command_namespace($this, $this->update);
		}
		return parent::getCommandObject($command);
	}
}
