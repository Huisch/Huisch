<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HouseRepository")
 */
class House {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(type="bigint")
	 */
	private $chatID;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Resident", mappedBy="house", orphanRemoval=true)
	 */
	private $residents;

	public function __construct($name, $chatID) {
		$this->residents = new ArrayCollection();
		$this->setName($name);
		$this->setChatID($chatID);
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}

	public function getChatID(): ?string {
		return $this->chatID;
	}

	public function setChatID(string $chatID): self {
		$this->chatID = $chatID;

		return $this;
	}

	/**
	 * @return Collection|Resident[]
	 */
	public function getResidents(): Collection {
		return $this->residents;
	}

	/**
	 * @return Collection|Resident[]
	 */
	public function getActiveResidents(): Collection {
		return $this->residents->filter(function(Resident $r) {
			return !$r->isDeleted();
		});
	}

	public function addResident(Resident $resident): self {
		if (!$this->residents->contains($resident)) {
			$this->residents[] = $resident;
			$resident->setHouse($this);
		}

		return $this;
	}

	public function removeResident(Resident $resident): self {
		if ($this->residents->contains($resident)) {
			$this->residents->removeElement($resident);
			// set the owning side to null (unless already changed)
			if ($resident->getHouse() === $this) {
				$resident->setHouse(null);
			}
		}

		return $this;
	}

	/**
	 * @param $text
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function sendMessage($text) {
		return Request::sendMessage([
			'chat_id' => $this->getChatID(),
			'text' => $text
		]);
	}

	public function getResidentsPlural($singular, $plural) {
		if ($this->getActiveResidents()->count() === 1) {
			return $singular;
		} else {
			return $plural;
		}
	}

	public function getResidentsString() {
		$residents = $this->getActiveResidents()->map(function(Resident $r) { return $r->getName(); });
		if ($residents->count() > 1) {
			$residents = $residents->toArray();
			$begin = array_slice($residents, 0, -1);
			$end = $residents[-1];
			return implode(', ', $begin) . ' en ' . $end;
		} else {
			return $residents->first();
		}
	}
}
