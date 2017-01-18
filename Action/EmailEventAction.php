<?php

namespace Ins\SendGridBundle\Action;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Ins\SendGridBundle\Entity\EmailEvent;
use Ins\SendGridBundle\Event\EventType;
use Ins\SendGridBundle\Event\WebHookEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Ins\SendGridBundle\Entity\Email;

class EmailEventAction
{
	/**
	 * @var Serializer
	 */
	private $serializer;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var EventDispatcher
	 */
	private $eventDispatcher;

	public function __construct(Serializer $serializer, EntityManager $entityManager, EventDispatcher $eventDispatcher) {
		$this->serializer = $serializer;
		$this->entityManager = $entityManager;
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * @param Request $request
	 * @return Response
	 *
	 * @Route(
	 *     name="send_grid_email_event",
	 *     path="/webhook/sendgrid/event",
	 * )
	 * @Method("POST")
	 */
	public function __invoke(Request $request)
	{
		if ($request->getContentType() !== 'json')
		{
			return null;
		}

		foreach (json_decode($request->getContent(), true) AS $emailEventArray) {
			$eventDate = new \DateTime('now');
			$eventDate->setTimestamp($emailEventArray['timestamp']);

			$emailEventArray['event'] = EventType::PREFIX_INCOMING . $emailEventArray['event'];
			$emailEventArray['smtpId'] = isset($emailEventArray['smtp-id']) ? $emailEventArray['smtp-id'] : null;
			$emailEventArray['eventDate'] = $eventDate->format('d-m-Y H:i:s');

			/** @var EmailEvent $emailEvent */
			$emailEvent = $this->serializer->deserialize(json_encode($emailEventArray), 'Ins\SendGridBundle\Entity\EmailEvent', $request->getContentType());

			if (isset($emailEventArray['email-entity-uuid']) && isset($emailEventArray['email-entity-class'])) {
				$emailEntity = $this->entityManager->find($emailEventArray['email-entity-class'], $emailEventArray['email-entity-uuid']);
				if ($emailEntity) {
					$emailEvent->setEmailEntity($emailEntity);
					$this->entityManager->persist($emailEvent);
				}
			}

			$this->eventDispatcher->dispatch($emailEventArray['event'], new WebHookEvent($emailEventArray['event'], $emailEvent));
		}

		$this->entityManager->flush();

		return new Response(null, Response::HTTP_CREATED);
	}
}
