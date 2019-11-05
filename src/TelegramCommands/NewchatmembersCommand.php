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
class NewchatmembersCommand extends SystemCommand {
	protected $name = 'newchatmembers';
	protected $description = 'New Chat Members';
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
		$members = $message->getNewChatMembers();

		if (!$message->botAddedInChat()) {
			$house = $this->em->getRepository(House::class)->findByChat($chat);

			foreach ($members as $member) {
				if (!$member->getIsBot()) {
					$this->em->getRepository(Resident::class)->findByHouseAndUser($house, $member);
				}
			}
		} else {
			Request::emptyResponse();
		}

		return Request::emptyResponse();
	}
}
