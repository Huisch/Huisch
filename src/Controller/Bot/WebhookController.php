<?php

namespace App\Controller\Bot;

use App\HuischTelegram;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController {
	/**
	 * @Route("/bot/{token}/webhook", name="bot_webhook")
	 * @param LoggerInterface $logger
	 * @param string $token
	 * @param KernelInterface $kernel
	 * @return JsonResponse
	 */
	public function webhook(LoggerInterface $logger, string $token, KernelInterface $kernel) {
		$key = $this->getParameter('telegram.api-key');
		$username = $this->getParameter('telegram.username');
		if ($key !== $token) {
			throw $this->createAccessDeniedException();
		}

		try {
			$telegram = new HuischTelegram($key, $username);
			$telegram->addCommandsPath($kernel->getProjectDir() . "/src/TelegramCommands");

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
