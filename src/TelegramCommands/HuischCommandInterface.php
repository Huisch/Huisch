<?php

namespace App\TelegramCommands;

interface HuischCommandInterface {
	/**
	 * Show in help when current environment is group or not.
	 * @param bool $group
	 * @return bool
	 */
	public function showWhen(bool $group);
}
