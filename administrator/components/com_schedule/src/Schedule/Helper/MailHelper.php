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
	public $contentType = "text/html";

	/**
	 * Property charset.
	 *
	 * @var  string
	 */
	public $charset = "utf-8";

	/**
	 * Property from.
	 *
	 * @var  string
	 */
	public $from = 'ihealth@ihealth.com.tw';

	/**
	 * sendMailWhenScheduleChange
	 *
	 * @param   string      $mailTo
	 * @param   stdClass    $displayData
	 *
	 * @return  MailHelper
	 *
	 * @throws \Exception
	 */
	public function sendMailWhenScheduleChange($mailTo, $displayData)
	{
		$mailer = \JFactory::getMailer();

		// TODO: 確認標題文字
		$mailer->setSubject("處方預約確認信");
		$mailer->setBody((new FileLayout("scedule.mail.confirm"))->render($displayData));
		$mailer->addRecipient($mailTo);
		$mailer->setSender($this->from);

		$mailer->Encoding = $this->charset;

		switch ($this->contentType)
		{
			case "text/html":
				$mailer->isHtml(true);
				break;
			case "text/plain":
				$mailer->isHtml(false);
				break;
			default:
				$mailer->isHtml(true);
		}

		$sendMailDone = $mailer->Send();

		if (! $sendMailDone)
		{
			throw new \Exception("Email send failure");
		}

		return $this;
	}
}
