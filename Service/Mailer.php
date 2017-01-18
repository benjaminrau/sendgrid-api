<?php

namespace Ins\SendGridBundle\Service;

use Behat\Testwork\ServiceContainer\Exception\ConfigurationLoadingException;
use Doctrine\ORM\EntityManager;
use SendGrid\Content;
use SendGrid\Email;
use SendGrid\Mail;
use Ins\SendGridBundle\Entity\Email as EmailEntity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Mailer
 * @package Ins\SendGridBundle\Service
 */
class Mailer
{
	/**
	 * @var \SendGrid
	 */
	private $sendGrid;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var string
	 */
	private $template = 'SendGridBundle::default.html.twig';

	/**
	 * @var TokenStorage
	 */
	private $tokenStorage;

    /**
     * @var boolean
     */
    private $disableDelivery;

    /**
     * @var boolean
     */
    private $enableRestriction;

    /**
     * @var array
     */
    private $patterns;

    /**
     * @var string
     */
    private $deliveryAddress;

	public function __construct(
	    EntityManager $entityManager, TokenStorage $tokenStorage, \Twig_Environment $twig
    )
	{
		$this->entityManager = $entityManager;
		$this->tokenStorage = $tokenStorage;
		$this->twig = $twig;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param array $configuration
	 */
	public function setConfiguration($configuration)
	{
	    $requiredConfKeys = ['apikey', 'disable_delivery', 'enable_restriction', 'delivery_address', 'patterns'];

        foreach ($requiredConfKeys as $requiredConfKey) {
            if (!array_key_exists($requiredConfKey, $configuration)) {
                throw new ConfigurationLoadingException("send_grid.{$requiredConfKey} is required configuration value.");
            }
        }

		$this->sendGrid = new \SendGrid($configuration['apikey']);

        $this->disableDelivery = $configuration['disable_delivery'];
        $this->enableRestriction = $configuration['enable_restriction'];
        $this->deliveryAddress = $configuration['delivery_address'];
        $this->patterns = $configuration['patterns'];
	}

	/**
	 * @param string 		$fromAddress
	 * @param string 		$toAddress
	 * @param array 		$context
	 * @param EmailEntity 	$emailEntity 	When given, the object is persisted and uuid is passed as smtp-x arg, to make email traceable
	 * @return mixed						The resonse from sendgrid v3 api
	 */
	public function sendMail($fromAddress, $toAddress, $context, EmailEntity $emailEntity = null) {
        if ($this->checkRestrictions($toAddress)) {

            $from = new Email(null, $fromAddress);
            $to = new Email(null, $toAddress);

            $context = $this->twig->mergeGlobals($context);
            $template = $this->twig->loadTemplate($this->getTemplate());
            $subject = $template->renderBlock('subject', $context);
            $htmlBody = $template->renderBlock('body_html', $context);

            $content = new Content("text/html", $htmlBody);

            $sendGridMail = new Mail($from, $subject, $to, $content);

            if ($emailEntity instanceof EmailEntity) {
                $this->saveEmailEntityAndStoreInUniqueArgs($sendGridMail, $emailEntity);
            }

            return $this->sendGrid->client->mail()->send()->post($sendGridMail);
        }
	}

	/**
	 * @param Mail $mail
	 * @param EmailEntity $emailEntity
	 */
	private function saveEmailEntityAndStoreInUniqueArgs(Mail $mail, EmailEntity $emailEntity) {
		$this->entityManager->persist($emailEntity);
		$this->entityManager->flush();

		// Adding the uuid of Email object in smtp-x args allows us to process incoming webhooks events for the sendgrid email later
		$mail->addCustomArg('email-entity-class', get_class($emailEntity));
		$mail->addCustomArg('email-entity-uuid', $emailEntity->getId());

		// When email is send within a authenticated FE request, add user UUID to smtp-x args, for debugging in case of spam or similar
		if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser()) {
			$mail->addCustomArg('user-entity-class', get_class($this->tokenStorage->getToken()->getUser()));
			$mail->addCustomArg('user-entity-uuid', $this->tokenStorage->getToken()->getUser()->getId());
		}
	}

    /**
     * @param $toAddress
     * @return bool
     */
    private function checkRestrictions(&$toAddress) {
        $send = !$this->disableDelivery;

        if ($send && $this->enableRestriction) {
            $patterns = $this->patterns;
            $match = false;
            try {
                foreach ($patterns as $pattern) {
                    if (preg_match('/' . $pattern . '/', $toAddress, $matches)) {
                        $match = true;
                        break;
                    }
                }
            } catch (\Exception $e) {}

            if ($match === false) {
                if ($this->deliveryAddress) {
                    $toAddress = $this->deliveryAddress;
                } else {
                    $send = false;
                }
            }
        }

        return $send;
    }
}
