<?php

namespace App\TelegramCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * User "/help" command
 *
 * Command that lists all available commands and displays them in User and Admin sections.
 */
class HelpCommand extends UserCommand implements HuischCommandInterface
{
    protected $name = 'help';
    protected $description = 'Toont mogelijke commando\'s';
    protected $usage = '/help or /help <command>';
    protected $version = '1.4.0';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $message     = $this->getMessage();
        $chat_id     = $message->getChat()->getId();
        $command_str = trim($message->getText(true));

        // Admin commands shouldn't be shown in group chats
        $safe_to_show = $message->getChat()->isPrivateChat();
        $isGroup = $message->getChat()->isGroupChat();

        $data = [
            'chat_id'    => $chat_id,
            'parse_mode' => 'markdown',
        ];

        list($all_commands, $user_commands, $admin_commands) = $this->getUserAdminCommands();

        // If no command parameter is passed, show the list.
        if ($command_str === '') {
            $data['text'] = '*Commando\'s*:' . PHP_EOL;
            foreach ($user_commands as $user_command) {
            	if ($user_command instanceof HuischCommandInterface && !$user_command->showWhen($isGroup)) continue;
                $data['text'] .= '/' . $user_command->getName() . ' - ' . $user_command->getDescription() . PHP_EOL;
            }

            if ($safe_to_show && count($admin_commands) > 0) {
                $data['text'] .= PHP_EOL . '*Administrator commando\'s*:' . PHP_EOL;
                foreach ($admin_commands as $admin_command) {
                	if ($admin_command instanceof HuischCommandInterface && !$admin_command->showWhen($isGroup)) continue;
                    $data['text'] .= '/' . $admin_command->getName() . ' - ' . $admin_command->getDescription() . PHP_EOL;
                }
            }

            $data['text'] .= PHP_EOL . 'Voor details, typ: /help <command>';

            return Request::sendMessage($data);
        }

        $command_str = str_replace('/', '', $command_str);
        if (isset($all_commands[$command_str]) && ($safe_to_show || !$all_commands[$command_str]->isAdminCommand()) && (!$all_commands[$command_str] instanceof HuischCommandInterface || $all_commands[$command_str]->showWhen($isGroup))) {
            $command      = $all_commands[$command_str];
            $data['text'] = sprintf(
                'Commando: %s' . PHP_EOL .
                'Beschrijving: %s' . PHP_EOL .
                'Gebruik: %s',
                $command->getName(),
                $command->getDescription(),
                $command->getUsage()
            );

            return Request::sendMessage($data);
        }

        $data['text'] = 'Geen hulp beschikbaar: commando /' . $command_str . ' niet gevonden';

        return Request::sendMessage($data);
    }

	/**
	 * Get all available User and Admin commands to display in the help list.
	 *
	 * @return Command[][]
	 * @throws TelegramException
	 */
    protected function getUserAdminCommands()
    {
        // Only get enabled Admin and User commands that are allowed to be shown.
        /** @var Command[] $commands */
        $commands = array_filter($this->telegram->getCommandsList(), function ($command) {
            /** @var Command $command */
            return !$command->isSystemCommand() && $command->showInHelp() && $command->isEnabled();
        });

        $user_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isUserCommand();
        });

        $admin_commands = array_filter($commands, function ($command) {
            /** @var Command $command */
            return $command->isAdminCommand();
        });

        ksort($commands);
        ksort($user_commands);
        ksort($admin_commands);

        return [$commands, $user_commands, $admin_commands];
    }

	/**
	 * Show in help when current environment is group or not.
	 * @param bool $group
	 * @return bool
	 */
	public function showWhen(bool $group) {
		return true;
	}
}
