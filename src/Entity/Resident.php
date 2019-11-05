<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResidentRepository")
 */
class Resident {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $telegramID;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $lastName;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\House", inversedBy="residents")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $house;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $deleted = false;

	/**
	 * Resident constructor.
	 * @param $telegramID
	 * @param $firstName
	 * @param $lastName
	 * @param $house
	 */
	public function __construct($telegramID, $firstName, $lastName, House $house) {
		$this->telegramID = $telegramID;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$house->addResident($this);
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function getTelegramID(): ?int {
		return $this->telegramID;
	}

	public function setTelegramID(int $telegramID): self {
		$this->telegramID = $telegramID;

		return $this;
	}

	public function getFirstName(): ?string {
		return $this->firstName;
	}

	public function setFirstName(string $firstName): self {
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string {
		return $this->lastName;
	}

	public function setLastName(?string $lastName): self {
		$this->lastName = $lastName;

		return $this;
	}

	public function getHouse(): ?House {
		return $this->house;
	}

	public function setHouse(?House $house): self {
		$this->house = $house;

		return $this;
	}

	public function isDeleted(): ?bool {
		return $this->deleted;
	}

	public function setDeleted(bool $deleted): self {
		$this->deleted = $deleted;

		return $this;
	}

	/**
	 * @param $text
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function sendMessage($text) {
		return Request::sendMessage([
			'chat_id' => $this->getTelegramID(),
			'text' => $text
		]);
	}

	public function getName() {
		return $this->getFirstName();
	}

	/**
	 * @throws TelegramException
	 */
	public function delete() {
		$this->setDeleted(true);
		$this->sendMessage("Je bent niet langer bewoner van {$this->getHouse()->getName()}.");
	}
}
