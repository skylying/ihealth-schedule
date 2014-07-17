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
	 * @param   string|array  $mailTo
	 * @param   mixed         $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendMailWhenScheduleChange($mailTo, $displayData)
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.confirm", SCHEDULE_ADMIN . '/layouts');

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
	 * @param   string|array  $mailTo
	 * @param   mixed         $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendEmptyRouteMail($mailTo, $displayData)
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.emptyroute", SCHEDULE_ADMIN . '/layouts');

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

	/**
	 * scheduleChangeNotify
	 *
	 * @param   string|array  $mailTo
	 * @param   mixed         $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function scheduleChangeNotify($mailTo, $displayData = array())
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.notify_staff", SCHEDULE_ADMIN . '/layouts');

		$mailer->setSubject("排程修改通知");
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
