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
	 * Property body.
	 *
	 * @var  string
	 */
	public $body = "";

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
	 * Property subject.
	 *
	 * @var  string
	 */
	public $subject = "";

	/**
	 * Property from.
	 *
	 * @var  string
	 */
	public $from = 'ihealth@ihealth.com.tw';

	/**
	 * Property to.
	 *
	 * @var  array
	 *
	 * 變數設定參考
	 * ```php
	 * array(
	 *     'a@user.mail',
	 *     'b@user.mail',
	 * )
	 * ```
	 */
	public $to = array();

	/**
	 * Property cc.
	 *
	 * @var  array
	 *
	 * 變數設定參考
	 * ```php
	 * array(
	 *     'a@user.mail',
	 *     'b@user.mail',
	 * )
	 * ```
	 */
	public $cc = array();

	/**
	 * Send Mail
	 *
	 * @return  MailHelper
	 *
	 * @throws  \Exception
	 */
	public function sendMail()
	{
		$mailer = \JFactory::getMailer();

		if (empty($this->subject))
		{
			throw new \Exception("Please input some subject.");
		}

		if (empty($this->body))
		{
			throw new \Exception("Please input some content.");
		}

		if (empty($this->charset))
		{
			throw new \Exception("Please input content char encoding");
		}

		$mailer->Encoding = $this->charset;

		$mailer->setSubject($this->subject);
		$mailer->setBody($this->body);
		$mailer->setSender($this->from);

		foreach ($this->cc as $mailCCAddress)
		{
			$mailer->addCC($mailCCAddress);
		}

		foreach ($this->to as $mailToAddress)
		{
			$mailer->addRecipient($mailToAddress);
		}

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

	/**
	 * Send Mail When Schedule Change
	 *
	 * @param   \JUser      $user      Joomla user object
	 * @param   Data[]      $schedules Schedules data
	 * @param   Data        $rx        Rx data
	 * @param   bool        $ccBack    When param is true, it will send mail to ihealth.
	 *
	 * @return  MailHelper
	 */
	public function sendMailWhenScheduleChange($user, $schedules, $rx, $ccBack = false)
	{
		$mailDisplayData = (object) array(
			'user'     => $user,
			'schedule' => $schedules,
			'rx'       => $rx
		);

		// TODO: 確認標題
		$this->subject = "處方預約確認信";
		$this->body    = (new FileLayout("scedule.mail.default"))->render($mailDisplayData);
		$this->to[]    = $user->email;

		if ($ccBack)
		{
			// TODO: 確認給 ihealth 信箱的 mail address
			$this->cc[] = "ihealth@ihealth.com.tw";
		}

		$this->sendMail();

		return $this;
	}
}
