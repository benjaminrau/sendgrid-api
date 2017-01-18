<?php

namespace Ins\SendGridBundle\Event;

/**
 * Class EventType.
 *
 * @see https://sendgrid.com/docs/API_Reference/Webhooks/event.html#-Event-Types
 */
class EventType
{
	const Processed   = 'sendgrid.processed';
	const Dropped     = 'sendgrid.dropped';
	const Delivered   = 'sendgrid.delivered';
	const Deferred    = 'sendgrid.deferred';
	const Bounce      = 'sendgrid.bounce';
	const Open        = 'sendgrid.open';
	const Click       = 'sendgrid.click';
	const SpamReport  = 'sendgrid.spamreport';
	const Unsubscribe = 'sendgrid.unsubscribe';
	const Unknown 	  = 'sendgrid.unknown';

	const PREFIX_INCOMING = 'sendgrid.';
}
