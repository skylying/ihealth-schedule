<?php
/**
 * Part of ihealth-schedule project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Windwalker\Controller\Ajax\QuickaddController;
use Windwalker\Helper\ArrayHelper;
use Windwalker\Helper\LanguageHelper;
use Windwalker\Model\CrudModel;
use Windwalker\Joomla\DataMapper\DataMapper;
use Windwalker\Data\Data;
use Windwalker\Model\Exception\ValidateFailException;
use Joomla\Registry\Registry;
use Schedule\Table\Table;

/**
 * Class ScheduleControllerCustomerAjaxJson
 *
 * @since 1.0
 */
class ScheduleControllerCustomerAjaxQuickadd extends QuickaddController
{
	/**
	 * prepareExecute
	 *
	 * @return void
	 */
	protected function prepareExecute()
	{
		// Init Variables
		$data = $this->input->get($this->input->get('formctrl'), array(), 'array');

		$birthDate = $data['birth_date'];

		$result = new Registry;


		switch (true)
		{
			case (preg_match('/^[12][0-9]{3}[01][0-9][0-3][0-9]$/', $birthDate)) :
				break;
			case (preg_match('/^[12][0-9]{3}-[01][0-9]-[0-3][0-9]$/', $birthDate)) :
				break;
			default:
				$result->set('Result', false);

				// Return Error Message.
				$result->set('errorMsg', \JText::sprintf('請確認生日格式是否符合8位數字或(YYYY-MM-DD)格式。'));

				jexit($result);
		}

		parent::prepareExecute();
	}

    /**
	 * doExecute
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		// Init Variables
		$data   = $this->input->get($this->input->get('formctrl'), array(), 'array');

		$result = new Registry;
		$result->set('Result', false);

		$model_name = $this->input->get('model_name');
		$component  = $this->input->get('component');
		$extension  = $this->input->get('extension');

		// Include Needed Classes
		\JLoader::registerPrefix(ucfirst($component), JPATH_BASE . "/components/com_{$component}");
		\JForm::addFormPath(JPATH_BASE . "/components/com_{$component}/models/forms");
		\JForm::addFieldPath(JPATH_BASE . "/components/com_{$component}/models/fields");
		\JTable::addIncludePath(JPATH_BASE . "/components/com_{$component}/tables");
		LanguageHelper::loadLanguage($extension, null);

		// Get Model
		/** @var $model CrudModel */
		$model = $this->getModel("Customer", ucfirst($component));

		// TODO: Is there `CUSTOMER_MEMBER_MAPS` model ?
		$memberMapMapper = new DataMapper(Table::CUSTOMER_MEMBER_MAPS);

		// For WindWalker Component only
		if (method_exists($model, 'getFieldsName'))
		{
			$fields_name = $model->getFieldsName();
			$data        = ArrayHelper::pivotToTwoDimension($data, $fields_name);
		}

		// Check for validation errors.
		try
		{
			// Get Form
			if (method_exists($model, 'getForm'))
			{
				$form = $model->getForm($data, false);

				if (!$form)
				{
					$result->set('errorMsg', 'No form');

					jexit($result);
				}

				// Test whether the data is valid.
				$validData = $model->validate($form, $data);
			}
			else
			{
				$validData = $data;
			}

			// Do Save
			$model->save($validData);

			// Set Id
			$data['id'] = $model->getState()->get($model_name . '.id');

			$memberMapData = new Data(
				array(
					'member_id'   => $data['member_id'],
					'customer_id' => $data['id']
				)
			);

			// Create member mapping
			$memberMapMapper->createOne($memberMapData);
		}
		catch (ValidateFailException $e)
		{
			// Get the validation messages.
			$errors   = $e->getErrors();

			$errors = array_map(
				function($error)
				{
					return (string) $error->getMessage();
				},
				$errors
			);

			$result->set('errorMsg', $errors);

			exit($result);
		}
		catch (\Exception $e)
		{
			// Return Error Message.
			$result->set('errorMsg', \JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $e->getMessage()));

			jexit($result);
		}

		// Set Result
		$result->set('Result', true);
		$result->set('data', $data);

		jexit($result);
	}
}
