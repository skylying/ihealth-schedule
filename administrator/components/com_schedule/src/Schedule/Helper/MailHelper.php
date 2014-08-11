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

		$mailer->setSubject(sprintf('[iHealth] 處方預約確認: %s您好! 您的處方宅配已預約完成', $displayData['member']->name));
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

		$mailer->setSubject(sprintf('[無送藥路線] %s 宅配日期: %s', $displayData['memberName'], $displayData['date']));
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

	/**
	 * getNotifyEmptyRouteMails
	 *
	 * @return  array
	 */
	public static function getNotifyEmptyRouteMails()
	{
		static $mails = null;

		if (is_array($mails))
		{
			return $mails;
		}

		$mails = \JComponentHelper::getParams('com_schedule')->get("schedule.empty_route_mail", array());

		return $mails;
	}

	/**
	 * sendCancelLayout
	 *
	 * @param   string|array $mailTo
	 * @param   mixed        $Data
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendCancelLayout($mailTo, $Data)
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.cancel", SCHEDULE_ADMIN . '/layouts');

		$mailer->setSubject("排程取消通知");
		$mailer->setBody($layout->render($Data));
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
