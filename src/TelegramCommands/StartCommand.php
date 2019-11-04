<?php

namespace App\TelegramCommands;

use App\Entity\House;
use App\Entity\Resident;
use App\Exception\InternalHuischException;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Start command
 */
class StartCommand extends SystemCommand {
	protected $name = 'start';
	protected $description = 'Start command';
	protected $usage = '/start';
	protected $version = '1.1.0';
	private $em;

	public function __construct(Telegram $telegram, Update $update = null, EntityManagerInterface $em = null) {
    	$this->em = $em;
		parent::__construct($telegram, $update);
	}

	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 * @throws InternalHuischException
	 */
	public function execute() {
		$message = $this->getMessage();
		$chat = $message->getChat();
		$user = $message->getFrom();
		if ($chat->isGroupChat()) {
			$house = $this->em->getRepository(House::class)->findByChat($chat);
			$this->em->getRepository(Resident::class)->findByHouseAndUser($house, $user);
			return $house->sendMessage("{$house->getName()} heeft nu {$house->getResidents()->count()} {$house->getResidentsPlural('bewoner', 'bewoners')}: {$house->getResidentsString()}.");
		} else {
			$resident = $this->em->getRepository(Resident::class)->findByUser($user);
			if (!$resident) {
				return Request::sendMessage([
					'chat_id' => $chat->getId(),
					'text' => "Welkom! Voeg me toe aan een groep en typ /start in de groep om me te gebruiken."
				]);
			} else {
				return $resident->sendMessage("Je bent nu actief in {$resident->getHouse()->getName()}.");
			}
		}
	}
}
