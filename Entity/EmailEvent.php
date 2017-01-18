<?php

namespace Ins\SendGridBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Ins\SendGridBundle\Event\EventType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource(
 * 		itemOperations={
 * 			"get"={"method"="GET"}
 *     },
 * 		collectionOperations={}
 * )
 * @ORM\Entity
 * @ORM\Table(name="EmailEvent")
 */
class EmailEvent
{
    /**
	 * @var string
	 *
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({
     *     "api_email_get_item"
     * })
     */
    private $event;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $smtpId;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Groups({
	 *     "api_email_get_item"
	 * })
	 */
	private $eventDate;

	/**
	 * @ORM\ManyToOne(targetEntity="Ins\SendGridBundle\Entity\Email", inversedBy="triggeredEvents")
	 * @ORM\JoinColumn(name="email_entity_id", referencedColumnName="id", nullable=true)
	 */
	private $emailEntity;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * @param string $event
	 */
	public function setEvent($event) {
		$this->event = $event;
	}

	/**
	 * @return string
	 */
	public function getSmtpId() {
		return $this->smtpId;
	}

	/**
	 * @param string $smtpId
	 */
	public function setSmtpId($smtpId) {
		$this->smtpId = $smtpId;
	}

	/**
	 * @return \DateTime
	 */
	public function getEventDate() {
		return $this->eventDate;
	}

	/**
	 * @param \DateTime $eventDate
	 */
	public function setEventDate(\DateTime $eventDate) {
		$this->eventDate = $eventDate;
	}

	/**
	 * @return Email
	 */
	public function getEmailEntity() {
		return $this->emailEntity;
	}

	/**
	 * @param Email $emailEntity
	 */
	public function setEmailEntity(Email $emailEntity) {
		$this->emailEntity = $emailEntity;
	}
}
