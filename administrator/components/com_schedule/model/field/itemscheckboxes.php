<?php

/**
 * Class JFormFieldSqlcheckboxes
 *
 * #xml 使用方法
 *
 * table         資料表名稱
 * select        搜尋取得的欄位
 *
 * id_key        篩選用的值
 * where_item_id 篩選的欄位
 *
 * value_field   input 值得欄位
 * option_title  input 顯示的標題
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
		$app         = JFactory::getApplication();
		$doc         = $app->getDocument();
		$name        = $this->name;
		$inputName   = $this->element['name'] ? (string) $this->element['name'] : "";
		$valueField  = $this->element['value_field'] ? (string) $this->element['value_field'] : "id";
		$optionTitle = $this->element['option_title'] ? (string) $this->element['option_title'] : "title";

		$doc->addScriptDeclaration(<<<JS
	jQuery(function()
	{
		jQuery('#{$inputName}-click-all').click(function()
		{
			if(jQuery("#{$inputName}-click-all").prop("checked"))
			{
				jQuery(".{$inputName}-checkboxes").each(function()
				{
					jQuery(this).prop("checked", true);
				});
			}
			else
			{
				jQuery(".{$inputName}-checkboxes").each(function()
				{
					jQuery(this).prop("checked", false);
				});
			}
		});
	});
JS
		);

		$items = $this->getItems();

		$html = array();

		$html[] = "<ol>";

		$html[] = <<<HTML
	<li class="checkbox-inline">
		<label for="{$inputName}-click-all">
			<input id="{$inputName}-click-all" type="checkbox" />
			全選
		</label>
	</li>
HTML;

		foreach ($items as $i => $item)
		{
			$id = $item->$valueField;
			$title = $item->$optionTitle;

			$html[] = <<<HTML
	<li class="checkbox-inline">
		<label for="{$name}-{$i}">
			<input id="{$name}-{$i}" class="{$inputName}-checkboxes" type="checkbox" name="{$name}" value="{$id}" />
			{$title}
		</label>
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
		$select      = $this->element['select'] ? (string) $this->element['select'] : "*";
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
