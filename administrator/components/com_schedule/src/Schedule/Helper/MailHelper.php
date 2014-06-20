<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;

/**
 * Class ScheduleHelper
 *
 * @since 1.0
 */
class MailHelper
{
	/**
	 * Property contentType.
	 *
	 * @var  string
	 */
	public static $contentType = "text/html";

	/**
	 * Property charset.
	 *
	 * @var  string
	 */
	public static $charset = "utf-8";

	/**
	 * Property from.
	 *
	 * @var  string
	 */
	public static $from = 'ihealth@ihealth.com.tw';

	/**
	 * sendMailWhenScheduleChange
	 *
	 * @param   string      $mailTo
	 * @param   stdClass    $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendMailWhenScheduleChange($mailTo, $displayData)
	{
		static::sendMailProcessor($mailTo, "處方預約確認信", "schedule.mail.confirm", $displayData);
	}

	/**
	 * sendEmptyRouteMail
	 *
	 * @param   string      $mailTo
	 * @param   stdClass    $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendEmptyRouteMail($mailTo, $displayData)
	{
		static::sendMailProcessor($mailTo, "沒有路線通知", "schedule.mail.emptyroute", $displayData);
	}

	/**
	 * Send Mail Processor
	 *
	 * @param   string  $mailTo
	 * @param   string  $subject
	 * @param   string  $layout
	 * @param   object  $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendMailProcessor($mailTo, $subject, $layout, $displayData)
	{
		$mailer = \JFactory::getMailer();

		$mailer->setSubject($subject);
		$mailer->setBody((new FileLayout($layout))->render($displayData));
		$mailer->addRecipient($mailTo);
		$mailer->setSender(static::$from);
		$mailer->isHtml("text/plain" !== static::$contentType);

		$mailer->CharSet = static::$charset;

		$sendMailDone = $mailer->Send();

		if (! $sendMailDone)
		{
			throw new \Exception("Email send failure");
		}
	}
}
