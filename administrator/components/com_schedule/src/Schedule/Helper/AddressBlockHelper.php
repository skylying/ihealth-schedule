<?php
/**
 * Part of schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Schedule\Helper;

use \Schedule\Helper\AddressHelper;

/**
 * Class AddressBlockHelper
 *
 * @since 1.0
 */
class AddressBlockHelper
{
	/**
	 * Render single address row
	 *
	 * @param int    $cityId
	 * @param int    $areaId
	 * @param string $roadName
	 * @param int    $previous
	 *
	 * @return  string
	 */
	public static function getAddressBlock($cityId, $areaId, $roadName, $previous)
	{
		$cityList = AddressHelper::getCityList('city', array('class' => 'form-control citylist'), 'value', 'text', $cityId);
		$areaList = AddressHelper::getAreaList($cityId, 'area', array('class' => 'form-control arealist'), 'value', 'text', $areaId);
		$spanClass = ($previous) ? 'glyphicon glyphicon-ok default' : 'glyphicon glyphicon-ok';
		$title = ($previous) ? 'true' : 'false';

		$html = <<<HTML
		<div class="row address-row" style="margin-bottom:20px;">
			<div class="col-md-1 visibleinput">
				<span class="{$spanClass}" title="{$title}"></span>
			</div>
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-6 citydiv">{$cityList}</div>
					<div class="col-md-6 areadiv">{$areaList}</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<input type="text" class="form-control roadname" style="margin-top:10px;" value="{$roadName}"/>
					</div>
				</div>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-danger deleteaddress">
					<span class="glyphicon glyphicon-trash"></span>
				</button>
			</div>
		</div>
HTML;

		return $html;
	}
}
