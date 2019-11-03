<?php

namespace App\Controller\Bot;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController {
	/**
	 * @Route("/bot/{token}/webhook", name="bot_webhook")
	 * @param LoggerInterface $logger
	 * @param string $token
	 * @return JsonResponse
	 */
	public function webhook(LoggerInterface $logger, string $token) {
		$key = $this->getParameter('telegram.api-key');
		$username = $this->getParameter('telegram.username');
		if ($key !== $token) {
			throw $this->createAccessDeniedException();
		}

		try {
			$telegram = new Telegram($key, $username);
			$telegram->addCommandsPath($this->get('kernel')->getProjectDir() . "/src/TelegramCommands");

			$result = $telegram->handle();
			return $this->json($result);
		} catch (TelegramException $e) {
			$logger->error($e->getMessage(), [
				'trace' => $e->getTraceAsString()
			]);
			return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
		}
	}
}
