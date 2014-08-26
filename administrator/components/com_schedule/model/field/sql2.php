<?php

JFormHelper::loadFieldClass('sql');

/**
 * Class JFormFieldSQLs
 *
 * A field type to enhance query property with Joomla SQL field type
 *
 * XML Properties:
 * - query: (Required) SQL statement to get initial item data
 *          SQL statement can use "%s" keyword to be replace by field value
 *          Field value will give a string combine all values with comma separators
 *          When multiple = true
 *              For example, give a query "SELECT * FROM some_table WHERE id IN (%s)"
 *              When field value is array(1, 2), the query will be "SELECT * FROM some_table WHERE id IN (1,2)"
 *          When multiple = false
 *              For example, give a query "SELECT * FROM some_table WHERE id > 10 OR id='%s'"
 *              When field value is "1", the query will be "SELECT * FROM some_table WHERE id > 10 OR id='1'"
 */
class JFormFieldSQL2 extends JFormFieldSQL
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'sql2';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		// Replace '%s' in the SQL query string
		if ($return && strpos($this->query, '%s'))
		{
			$db = JFactory::getDbo();
			$replace = $db->quote($value);

			if (true === $this->multiple)
			{
				if (!is_array($replace) || empty($replace))
				{
					$replace = array('""');
				}

				$this->query = sprintf($this->query, implode(',', $replace));
			}
			else
			{
				$this->query = sprintf($this->query, (string) $replace);
			}
		}

		return $return;
	}
}
