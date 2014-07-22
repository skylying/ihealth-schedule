<?php
/**
 * Part of schedule project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use Windwalker\Html\HtmlElement;

/**
 * Class UiHelper
 *
 * @since 1.0
 */
abstract class UiHelper
{
	/**
	 * For some list page to quickly build edit link button.
	 *
	 * Usage: `echo UiHelper::editButton('address', $id);`
	 *
	 * @param   string  $view   View name to build task: `{view}.edit.edit`.
	 * @param   int     $id     Edit id.
	 * @param   string  $title  Title of link, default is an icon.
	 * @param   string  $tmpl   The edit layout.
	 * @param   array   $query  URL query array.
	 * @param   array   $attrs  Link element attributes.
	 *
	 * @return  string  Link element.
	 */
	public static function editButton($view, $id = null, $title = null, $tmpl = 'edit', $query = array(), $attrs = array())
	{
		$title = $title ? : new HtmlElement('span', null, ['class' => 'glyphicon glyphicon-edit']);

		$query['option'] = 'com_schedule';
		$query['task'] = $view . '.edit.edit';

		if ($tmpl)
		{
			$query['layout'] = $tmpl;
		}

		if ($id)
		{
			$query['id'] = $id;
		}

		$attrs['class'] = !empty($attrs['class']) ? $attrs['class'] : 'btn btn-primary btn-mini';

		$link = \JRoute::_('index.php?' . http_build_query($query));

		return \JHtml::link($link, $title, $attrs);
	}

	/**
	 * Make a link to direct to foreign table item.
	 * Note that the ordering or id and title are different from `editButton()`, but others are same.
	 *
	 * Usage: `echo UiHelper::foreignLink('customer', $item->customer_name, $item->customer_id);`
	 *
	 * @param   string  $view   View name to build task: `{view}.edit.edit`.
	 * @param   string  $title  Title of link, default is an icon.
	 * @param   int     $fk     Edit foreign id.
	 * @param   string  $tmpl   The edit layout.
	 * @param   array   $query  URL query array.
	 * @param   array   $attrs  Link element attributes.
	 *
	 * @return  string  Link element.
	 *
	 * @return  string
	 */
	public static function foreignLink($view, $title, $fk = null, $tmpl = 'edit', $query = array(), $attrs = array())
	{
		if (!$fk)
		{
			return '';
		}

		$title = $title . '<small class="glyphicon glyphicon-share"></small>';

		$attrs['class'] = 'text-muted';
		$attrs['target'] = '_blank';

		return static::editButton($view, $fk, $title, $tmpl, $query, $attrs);
	}
}
