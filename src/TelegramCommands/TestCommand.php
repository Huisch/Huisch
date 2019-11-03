<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Repository\HouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TestCommand extends UserCommand {
	protected $name = 'test';
    protected $description = 'A command to test';
    protected $usage = '/test';
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
		$houseRepo = $this->em->getRepository(HouseRepository::class);
		$count = count($houseRepo->findAll());

		$message = $this->getMessage();
		$chat = $message->getChat()->getId();
		$person = $message->getFrom()->getFirstName();

		return Request::sendMessage([
			'chat_id' => $chat,
			'text' => "Hallo {$person}, er zijn {$count} huizen gevonden."
		]);
	}
}
