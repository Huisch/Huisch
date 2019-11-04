<?php

namespace App\TelegramCommands;

use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

class BewonersCommand extends UserCommand {
	protected $name = 'bewoners';
    protected $description = 'Geeft een lijst van huidige bewoners.';
    protected $usage = '/bewoners';
    protected $version = '1.0.0';
    private $em;

    public function __construct(Telegram $telegram, Update $update = null, EntityManagerInterface $em = null) {
    	$this->em = $em;
		parent::__construct($telegram, $update);
	}

	/**
	 * Execute command
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute() {
		if (!$this->getMessage()->getChat()->isGroupChat()) {
			return $this->replyToChat("Dit commando kan alleen in groepen gebruikt worden.");
		}
		return $this->getTelegram()->executeCommand('start');
	}
}
