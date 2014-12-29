<?php

JFormHelper::loadFieldClass('calendar');

/**
 * Class JFormFieldCalendar2
 *
 * XML Properties:
 *
 * - @see JFormFieldCalendar
 */
class JFormFieldCalendar2 extends JFormFieldCalendar
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'calendar2';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		// Check date format
		if (false === $this->validateDateFormat($this->value))
		{
			$this->value = '';
		}

		$input = parent::getInput();

		return $input;
	}

	/**
	 * validateDateFormat
	 *
	 * @param   string  $date
	 *
	 * @return  bool
	 */
	private function validateDateFormat($date)
	{
		return (bool) preg_match("/^[1-2][0-9]{3}-(0{0,1}[1-9]|1[0-2])-(0{0,1}[1-9]|[1-2][0-9]|3[0-1])$/", $date);
	}
}
