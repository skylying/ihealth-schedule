<?php

/**
 * Class JFormFieldSqlcheckboxes
 *
 * @since 1.0
 */
class JFormFieldItemscheckboxes extends \JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Itemscheckboxes';

	/**
	 * getInput
	 *
	 * @return  string
	 */
	public function getInput()
	{
		$valueField = $this->element['value_field'] ? (string) $this->element['value_field'] : "id";
		$optionTitle = $this->element['option_title'] ? (string) $this->element['option_title'] : "title";

		$items = $this->getItems();

		$html = array();

		$html[] = "<ol>";

		$name = $this->name;

		foreach ($items as $i => $item)
		{
			$id = $item->$valueField;
			$title = $item->$optionTitle;

			$html[] = <<<HTML
	<li>
		<label for="{$name}-{$i}">{$title}</label>
		<input id="{$name}-{$i}" type="checkbox" name="{$name}" value="{$id}" />
	</li>
HTML;
		}

		$html[] = "</ol>";

		return implode($html);
	}

	/**
	 * getItems
	 *
	 * @return  array
	 */
	public function getItems()
	{
		$select      = $this->element['sql'] ? (string) $this->element['sql'] : "*";
		$idKey       = $this->element['id_key'] ? (string) $this->element['id_key'] : "id";
		$whereItemId = $this->element['where_item_id'] ? (string) $this->element['where_item_id'] : null;
		$table       = $this->element['table'] ? (string) $this->element['table'] : null;

		// 沒有設定 table
		if (empty($table))
		{
			return array();
		}

		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$input = $app->input;

		// Get id
		$id = $input->getInt($idKey);

		$q = $db->getQuery(true);

		$q->select($select)->from($table);

		// 如果有設定追蹤 item
		if (! empty($whereItemId))
		{
			// 如果有追蹤 id 但現在沒有 id
			if (empty($id))
			{
				return array();
			}

			// Get where
			$q->where("{$whereItemId} = {$id}");
		}

		$db->setQuery($q);

		$items = $db->loadObjectList();

		$items = $items ? $items : array();

		return $items;
	}
}
