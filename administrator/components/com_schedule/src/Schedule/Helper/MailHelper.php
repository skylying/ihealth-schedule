<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;

/**
 * Class MailHelper
 *
 * @since 1.0
 */
class MailHelper
{
	/**
	 * sendMailWhenScheduleChange
	 *
	 * @param   string      $mailTo
	 * @param   mixed       $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendMailWhenScheduleChange($mailTo, $displayData)
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');
		$layout = new FileLayout("schedule.mail.confirm", JPATH_ADMINISTRATOR . '/components/com_schedule/layouts');

		$mailer->setSubject("處方預約確認信");
		$mailer->setBody($layout->render($displayData));
		$mailer->addRecipient($mailTo);
		$mailer->setSender($from);
		$mailer->isHtml(true);

		$sendMailDone = $mailer->Send();

		if (! $sendMailDone)
		{
			throw new \Exception("Email send failure");
		}
	}

	/**
	 * sendEmptyRouteMail
	 *
	 * @param   string      $mailTo
	 * @param   mixed       $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendEmptyRouteMail($mailTo, $displayData)
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');
		$layout = new FileLayout("schedule.mail.emptyroute", JPATH_ADMINISTRATOR . '/components/com_schedule/layouts');

		$mailer->setSubject("沒有路線通知");
		$mailer->setBody($layout->render($displayData));
		$mailer->addRecipient($mailTo);
		$mailer->setSender($from);
		$mailer->isHtml(true);

		$sendMailDone = $mailer->Send();

		if (! $sendMailDone)
		{
			throw new \Exception("Email send failure");
		}
	}
}
