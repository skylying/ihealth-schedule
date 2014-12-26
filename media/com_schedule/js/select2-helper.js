/**
 * Class Select2Helper
 */
;(function($, undefined)
{
	"use strict";

	/**
	 * Class Select2Helper
	 *
	 * Configuration Properties:
	 *
	 * - idField:            (Required) Determine the id filed name, use this field name to get selection value
	 * - textField:          (Required) Determine the text filed name, use this field name to get selection display text
	 * - apiUrl:             (Optional) Ajax request url, MUST replace "&" with "&amp;" (Default: null)
	 *                       EX: "index.php?option=com_schedule&amp;task=institutes.search.json"
	 * - apiQueryKey:        (Optional) Query key name attached after ajax request url
	 *                       If ajax request is "index.php?option=com_schedule&task=institutes.search.json&filter_search="
	 *                       The apiQueryKey will be "filter_search"
	 * - apiDataType:        (Optional) Ajax data-type, could use "xml", "json" and "jsonp" (Default: "json")
	 * - minimumInputLength: (Optional) Number of characters necessary to start a search (Default: 2)
	 * - enableComboBox:     (Optional) Enable combo-box support, (Default: false)
	 * - data:               (Optional) Selection data (Default: [])
	 * - initialData:        (Optional) Setup a initial value object (Default: null)
	 * - readonly:           (Optional) Setup readonly property (Default: false)
	 * - onchange:           (Optional) Setup an onchange event callback (Default: null)
	 */
	window.Select2Helper = window.Select2Helper || {
		/**
		 * Select2 configuration collection with namespace
		 *
		 * Store different select2 configuration with different namespaces
		 *
		 * @type  {Object<string, Object>}
		 */
		configCollection: {},

		/**
		 * Set select2 configuration with a namespace
		 *
		 * @param  {string}  namespace  Select2 namespace
		 * @param  {Object}  config     Select2 configuration
		 */
		setConfig: function(namespace, config)
		{
			config = $.extend({
				minimumInputLength: 2,
				placeholder: '',
				apiUrl: null,
				apiDataType: 'json',
				apiQueryKey: 'q',
				idField: 'id',
				textField: 'text',
				data: [],
				initialData: null,
				enableComboBox: false,
				readonly: false,
				onchange: null,
				allowClear: false
			}, config);

			this.configCollection[namespace] = config;
		},

		/**
		 * Initialize select2 element
		 *
		 * @param  {string}  namespace  A Select2 namespace
		 * @param  {jQuery}  $node      A jQuery node
		 * @param  {Object}  config     (Optional) Select2 configuration
		 */
		select2: function(namespace, $node, config)
		{
			if (undefined === this.configCollection[namespace])
			{
				throw new Error('This namespace "' + namespace + '" is not exists!');
			}

			// Merge configuration
			config = $.extend(this.configCollection[namespace], config);

			var select2Option = {
				id: config.idField,
				minimumInputLength: config.minimumInputLength,
				placeholder: config.placeholder,
				allowClear: config.allowClear,
				formatResult: function(result)
				{
					return result[config.textField];
				},
				formatSelection: function(result)
				{
					return result[config.textField];
				},
				initSelection: function(element, callback)
				{
					if (null !== config.initialData &&
						undefined !== config.initialData[config.idField] &&
						undefined !== config.initialData[config.textField])
					{
						callback(config.initialData);
					}
				},
				createSearchChoice: function(term, data)
				{
					if (config.enableComboBox)
					{
						var termExists = data.some(function(r) { return r[config.textField] == term; }),
							ret = {};

						ret[config.idField] = term;
						ret[config.textField] = term + (termExists ? '' : ' (新)');
						ret['_new'] = !termExists;

						if (!termExists)
						{
							return ret;
						}
					}

					return null;
				}
			};

			if (config.apiUrl)
			{
				select2Option.ajax = {
					url: config.apiUrl,
					dataType: config.apiDataType,
					data: function(term)
					{
						var obj = {};

						obj[config.apiQueryKey] = term;

						return obj;
					},
					results: function(data)
					{
						return {results: data};
					}
				}
			}
			else
			{
				select2Option.data = {
					results: config.data,
					text: config.textField
				};
			}

			$node.select2(select2Option);

			if (config.readonly)
			{
				$node.select2('readonly', true);
			}

			$node.on('change', function(e)
			{
				if (typeof config.onchange === 'function')
				{
					config.onchange(e, $node);
				}
			});
		}
	};
})(jQuery);
