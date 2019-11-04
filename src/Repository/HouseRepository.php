<?php

namespace App\Repository;

use App\Entity\House;
use App\Exception\InternalHuischException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Exception\TelegramException;

/**
 * @method House|null find($id, $lockMode = null, $lockVersion = null)
 * @method House|null findOneBy(array $criteria, array $orderBy = null)
 * @method House[]    findAll()
 * @method House[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HouseRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, House::class);
	}

	/**
	 * @param Chat $chat
	 * @return House
	 * @throws InternalHuischException
	 * @throws TelegramException
	 */
	public function findByChat(Chat $chat) {
		if (!$chat->isGroupChat()) throw new InternalHuischException("Chat is not a group");
		$house = $this->findOneBy(['chatID' => $chat->getId()]);
		if (!$house) {
			$house = $this->createFromChat($chat);
			$house->sendMessage("Hoera! {$house->getName()} is nu geregistreerd in Huisch!");
		}
		return $house;
	}

	/**
	 * @param Chat $chat
	 * @return House
	 * @throws InternalHuischException
	 */
	private function createFromChat(Chat $chat) {
		if (!$chat->isGroupChat()) throw new InternalHuischException("Chat is not a group");
		$house = new House($chat->getTitle(), $chat->getId());
		/** @var EntityManagerInterface $entityManager */
		$entityManager = $this->getEntityManager();
		$entityManager->persist($house);
		$entityManager->flush();
		return $house;
	}
}
