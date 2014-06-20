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
		$mailer = \JFactory::getMailer();

		// TODO: 確認標題文字
		$mailer->setSubject("處方預約確認信");
		$mailer->setBody((new FileLayout("scedule.mail.confirm"))->render($displayData));
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
