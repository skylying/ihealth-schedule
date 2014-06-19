<?php

namespace Schedule\Helper;

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
}
