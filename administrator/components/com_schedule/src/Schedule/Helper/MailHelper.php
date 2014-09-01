<?php

namespace Schedule\Helper;

use Windwalker\Data\Data;
use Windwalker\View\Layout\FileLayout;
use Windwalker\System\ExtensionHelper;

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

		$params = ExtensionHelper::getParams('com_schedule');
		$displayData['ihealthSiteUrl'] = $params->get('ihealth_site.url', 'http://www.ihealth.com.tw');

		$mailer->setSubject(sprintf('[iHealth] 處方預約確認: %s 您好! 您的處方宅配已預約完成', $displayData['member']->name));
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

		$params = ExtensionHelper::getParams('com_schedule');
		$displayData['ihealthSiteUrl'] = $params->get('ihealth_site.url', 'http://www.ihealth.com.tw');

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

		$params = ExtensionHelper::getParams('com_schedule');
		$displayData['ihealthSiteUrl'] = $params->get('ihealth_site.url', 'http://www.ihealth.com.tw');

		$changedText = \JText::_('COM_SCHEDULE_EMAIL_TILE_SCHEDULE_' . $displayData['changed']);

		$displayData['changedText'] = $changedText;

		$mailer->setSubject(sprintf('[排程更改] %s%s %s 的外送排程', $displayData['memberName'], $changedText, $displayData['date']));
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
	 * @param   mixed        $displayData
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 */
	public static function sendCancelLayout($mailTo, $displayData = array())
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.cancel", SCHEDULE_ADMIN . '/layouts');

		$params = ExtensionHelper::getParams('com_schedule');
		$displayData['ihealthSiteUrl'] = $params->get('ihealth_site.url', 'http://www.ihealth.com.tw');

		$mailer->setSubject(sprintf('[iHealth] 取消確認: %s 您好! 您的處方宅配預約已取消', $displayData['member']->name));
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
	 * sendRegisteredLayout
	 *
	 * @param string|array $mailTo
	 * @param array	       $displayData
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function sendRegisteredLayout($mailTo, $displayData = array())
	{
		$mailer = \JFactory::getMailer();
		$from   = \JFactory::getConfig()->get('mailfrom');

		// Set layouts from admin
		$layout = new FileLayout("schedule.mail.registered", SCHEDULE_ADMIN . '/layouts');

		$params = ExtensionHelper::getParams('com_schedule');
		$displayData['ihealthSiteUrl'] = $params->get('ihealth_site.url', 'http://www.ihealth.com.tw');

		$mailer->setSubject(sprintf('[iHealth] 註冊成功: %s 您好! 恭喜您已註冊成功。', $displayData['name']));
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
