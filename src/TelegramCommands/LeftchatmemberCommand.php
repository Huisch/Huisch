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
 * New chat member command
 */
class LeftchatmemberCommand extends SystemCommand {
	protected $name = 'leftchatmember';
	protected $description = 'Left Chat Member';
	protected $version = '1.2.0';
	protected $show_in_help = false;
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
		$member = $message->getLeftChatMember();
		throw new InternalHuischException("That went wrong");

		if (!$member->getIsBot()) {
			$house = $this->em->getRepository(House::class)->findByChat($chat);
			/** @var Resident $resident */
			$resident = $this->em->getRepository(Resident::class)->findByHouseAndUser($house, $member);
			$resident->delete();
			$this->em->flush();
		}

		return Request::emptyResponse();
	}
}
