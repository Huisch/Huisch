<?php

namespace App\Repository;

use App\Entity\House;
use App\Entity\Resident;
use App\Exception\InternalHuischException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;

/**
 * @method Resident|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resident|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resident[]    findAll()
 * @method Resident[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResidentRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Resident::class);
	}

	/**
	 * @param House $house
	 * @param User $user
	 * @return Resident
	 * @throws InternalHuischException
	 * @throws TelegramException
	 */
	public function findByHouseAndUser(House $house, User $user) {
		if ($user->getIsBot()) throw new InternalHuischException("User is a bot");
		$foundResidents = $house->getResidents()->filter(function($resident) use ($user) {
			/** @var $resident Resident */
			return $resident->getTelegramID() === $user->getId();
		});
		if ($foundResidents->count() > 0) {
			return $foundResidents->first();
		} else {
			$resident = $this->createFromUser($house, $user);
			$resident->sendMessage("Gefeliciteerd! Je bent nu een bewoner van {$house->getName()}.");
			if ($this->count(['telegramID' => $resident->getTelegramID()]) > 1) {
				$resident->sendMessage("Let op. Omdat je in meerdere huizen zit, zullen sommige functies niet optimaal werken.");
			}
			return $resident;
		}
	}

	/**
	 * @param User $user
	 * @return Resident|null
	 * @throws InternalHuischException
	 */
	public function findByUser(user $user) {
		if ($user->getIsBot()) throw new InternalHuischException("User is a bot");
		$resident = $this->findOneBy(['telegramID' => $user->getId()]);
		return $resident;
	}

	/**
	 * @param House $house
	 * @param User $user
	 * @return Resident
	 * @throws InternalHuischException
	 */
	private function createFromUser(House $house, User $user) {
		if ($user->getIsBot()) throw new InternalHuischException("User is a bot");
		/** @var EntityManagerInterface $entityManager */
		$entityManager = $this->getEntityManager();

		$resident = new Resident($user->getId(), $user->getFirstName(), $user->getLastName(), $house);
		$entityManager->persist($resident);
		$entityManager->flush();
		return $resident;
	}
}
