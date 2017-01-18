<?php

namespace Ins\SendGridBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping AS ORM;
use Ins\AppBundle\Entity\Inquiry;
use Ins\AppBundle\Entity\InquiryAnswer;
use Ins\AppBundle\Entity\Organization;
use Ins\AppBundle\Entity\Visit;
use Ins\AppBundle\Entity\VisitAnswer;
use Ins\UserBundle\Entity\User;
use Ins\AppBundle\Entity\Pass;
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
 * @ORM\Table(name="Email")
 */
class Email
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
	 * @var Visit
	 *
     * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\Visit", inversedBy="sentEmails")
	 * @ORM\JoinColumn(name="triggering_visit_id", referencedColumnName="id", nullable=true)
     */
    private $triggeringVisit;

    /**
     * @var Visit
     *
     * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\VisitAnswer", inversedBy="sentEmails")
     * @ORM\JoinColumn(name="triggering_visit_answer_id", referencedColumnName="id", nullable=true)
     */
    private $triggeringVisitAnswer;

	/**
	 * @var Pass
	 *
	 * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\Pass", inversedBy="sentEmails")
	 * @ORM\JoinColumn(name="triggering_pass_id", referencedColumnName="id", nullable=true)
	 */
	private $triggeringPass;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Ins\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="triggering_user_id", referencedColumnName="id", nullable=true)
	 */
	private $triggeringUser;

	/**
	 * @var Inquiry
	 *
	 * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\Inquiry", inversedBy="sentEmails")
	 * @ORM\JoinColumn(name="triggering_inquiry_id", referencedColumnName="id", nullable=true)
	 */
	private $triggeringInquiry;

	/**
	 * @var InquiryAnswer
	 *
	 * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\InquiryAnswer", inversedBy="sentEmails")
	 * @ORM\JoinColumn(name="triggering_inquiry_answer_id", referencedColumnName="id", nullable=true)
	 */
	private $triggeringInquiryAnswer;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Ins\AppBundle\Entity\Organization", inversedBy="sentEmails")
     * @ORM\JoinColumn(name="triggering_organization_id", referencedColumnName="id", nullable=true)
     */
    private $triggeringOrganization;

	/**
	 * @var Collection<EmailEvent>
	 *
	 * @ORM\OneToMany(targetEntity="Ins\SendGridBundle\Entity\EmailEvent", mappedBy="emailEntity")
	 * @Groups({
	 *     "api_email_get_item"
	 * })
	 */
	private $triggeredEvents;

	public function __construct() {
		$this->triggeredEvents = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

    /**
     * @return VisitAnswer
     */
    public function getTriggeringVisitAnswer() {
        return $this->triggeringVisitAnswer;
    }

    /**
     * @param VisitAnswer $triggeringVisitAnswer
     */
    public function setTriggeringVisitAnswer(VisitAnswer $triggeringVisitAnswer) {
        $this->triggeringVisitAnswer = $triggeringVisitAnswer;
    }
    
	/**
	 * @return Visit
	 */
	public function getTriggeringVisit() {
		return $this->triggeringVisit;
	}

	/**
	 * @param Visit $triggeringVisit
	 */
	public function setTriggeringVisit(Visit $triggeringVisit) {
		$this->triggeringVisit = $triggeringVisit;
	}

	/**
	 * @return Pass
	 */
	public function getTriggeringPass()
	{
		return $this->triggeringPass;
	}

	/**
	 * @param Pass $triggeringPass
	 */
	public function setTriggeringPass($triggeringPass)
	{
		$this->triggeringPass = $triggeringPass;
	}

	/**
	 * @return Inquiry
	 */
	public function getTriggeringInquiry() {
		return $this->triggeringInquiry;
	}

	/**
	 * @param Inquiry $triggeringInquiry
	 */
	public function setTriggeringInquiry(Inquiry $triggeringInquiry) {
		$this->triggeringInquiry = $triggeringInquiry;
	}

	/**
	 * @return InquiryAnswer
	 */
	public function getTriggeringInquiryAnswer() {
		return $this->triggeringInquiryAnswer;
	}

	/**
	 * @param InquiryAnswer $triggeringInquiryAnswer
	 */
	public function setTriggeringInquiryAnswer(InquiryAnswer $triggeringInquiryAnswer) {
		$this->triggeringInquiryAnswer = $triggeringInquiryAnswer;
	}

	/**
	 * @return User
	 */
	public function getTriggeringUser() {
		return $this->triggeringUser;
	}

	/**
	 * @param User $triggeringUser
	 */
	public function setTriggeringUser(User $triggeringUser) {
		$this->triggeringUser = $triggeringUser;
	}

    /**
     * @return Organization
     */
    public function getTriggeringOrganization()
    {
        return $this->triggeringOrganization;
    }

    /**
     * @param Organization $triggeringOrganization
     */
    public function setTriggeringOrganization($triggeringOrganization)
    {
        $this->triggeringOrganization = $triggeringOrganization;
    }

	/**
	 * Add triggeredEvent
	 *
	 * @param \Ins\SendGridBundle\Entity\EmailEvent $triggeredEvent
	 */
	public function addTriggeredEvent(\Ins\SendGridBundle\Entity\EmailEvent $triggeredEvent)
	{
		$triggeredEvent->setEmailEntity($this);
		$this->triggeredEvents->add($triggeredEvent);
	}

	/**
	 * Remove triggeredEvents
	 *
	 * @param \Ins\SendGridBundle\Entity\EmailEvent $triggeredEvent
	 */
	public function removeTriggeredEvent(\Ins\SendGridBundle\Entity\EmailEvent $triggeredEvent)
	{
		$this->triggeredEvents->removeElement($triggeredEvent);
	}

	/**
	 * Get triggeredEvents
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTriggeredEvents()
	{
		return $this->triggeredEvents;
	}

	/**
	 * @param TokenInterface $token
	 * @return bool
	 */
	public function canUserEdit(TokenInterface $token) {
		return false;
	}

	/**
	 * @param TokenInterface $token
	 * @return bool
	 */
	public function canUserView(TokenInterface $token) {
		/** @var User $user */
		$user = $token->getUser();
		return
            $token->getUser()->hasRole('ROLE_SUPER_ADMIN') ||
			( $this->getTriggeringInquiry() &&
				(
					$user->getOrganization() === $this->getTriggeringInquiry()->getManufacturer()
				)
			) ||
			( $this->getTriggeringInquiryAnswer() &&
				(
					$user->getOrganization() === $this->getTriggeringInquiryAnswer()->getManufacturer()
				)
			) ||
            ( $this->getTriggeringPass() &&
                (
                    $user->getOrganization() === $this->getTriggeringPass()->getManufacturer()
                )
            ) ||
            ( $this->getTriggeringVisit() &&
                (
                    $user->getOrganization() === $this->getTriggeringVisit()->getPass()->getManufacturer()
                )
            ) ||
            ( $this->getTriggeringVisitAnswer() &&
                (
                    $user->getOrganization() === $this->getTriggeringVisitAnswer()->getAnsweredVisit()->getPass()->getManufacturer()
                )
            ) ||
            ( $this->getTriggeringUser() && $user === $this->getTriggeringUser());
	}

	public function canUserCreate(TokenInterface $token = null) {
		return true;
	}

	/**
	 * @param TokenInterface $token
	 * @return bool
	 */
	public static function canUserViewCollection(TokenInterface $token) {
		return $token->getUser()->hasRole('ROLE_SUPER_ADMIN');
	}
}
