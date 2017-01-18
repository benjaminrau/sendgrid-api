<?php

namespace Ins\SendGridBundle\Event;

use Ins\SendGridBundle\Entity\EmailEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class WebHookEvent.
 */
class WebHookEvent extends Event
{
	/**
	 * @var EmailEvent
	 */
	private $data;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @param string $type
	 * @param EmailEvent $data
	 */
	public function __construct($type, EmailEvent $data)
	{
		$this->data = $data;
		$this->type = $type;
	}
}
