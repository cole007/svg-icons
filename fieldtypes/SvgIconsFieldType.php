<?php
/**
 * SVG Icons plugin for Craft CMS
 *
 * Easily access urls or inline SVG icons from a public directory
 *
 * @author    Fyrebase
 * @copyright Copyright (c) 2016 Fyrebase
 * @link      fyrebase.com
 * @package   SvgIcons
 * @since     0.0.1
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/fyrebase/svg-icons
 */

namespace Craft;

class SvgIconsFieldType extends BaseFieldType
{
	/**
	 * Returns the name of the fieldtype.
	 *
	 * @return mixed
	 */
	public function getName()
	{
		return Craft::t('SVG Icons');
	}

	/**
	 * Returns the content attribute config.
	 *
	 * @return mixed
	 */
	public function defineContentAttribute()
	{
		return AttributeType::Mixed;
	}

	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
		$settings['iconSets'] = AttributeType::Mixed;

		return $settings;
	}

	/**
	 * Get settings html
	 * @return string
	 */
	public function getSettingsHtml()
	{
		$iconSetsPath = craft()->config->get('iconSetsPath', 'svgicons');
		$iconSets = array();
		$errors = array();

		if (IOHelper::folderExists($iconSetsPath))
		{
			$folders = IOHelper::getFolderContents($iconSetsPath, false);

			if (is_array($folders))
			{
				foreach ($folders as $idx => $f)
				{
					$iconSets[IOHelper::getFolderName($f) . IOHelper::getFolderName($f, false)] = IOHelper::getFolderName($f, false);

					// Create sprite sheet resources
					foreach (IOHelper::getFolderContents($f, false, '\.svg.css') as $stylesheet) {
						craft()->templates->includeCss(IOHelper::getFileContents($stylesheet));
						craft()->svgIcons->getSprites($stylesheet);
					}
				}
			}
			if (empty($iconSets)) {
				$errors = array_merge(
					array('<p class="warning"><strong>You don’t have any SVG Icons.</strong></p><p>Please ensure you have placed your SVG icon collections within <code>' . $iconSetsPath . '</code></p>'),
					$errors
				);
			}
		} else {
			$errors = array_merge(
				array('<p class="warning"><strong>Unable to locate SVG Icons source directory.</strong></p><p>Please ensure <code>' . $iconSetsPath . '</code> exists.</p>'),
				$errors
			);
		}

		craft()->templates->includeCssResource('svgicons/css/fields/SvgIconsFieldType_Settings.css');

		return craft()->templates->render('svgicons/fields/SvgIconsFieldType_Settings', array(
			'settings' => $this->getSettings(),
			'iconSets' => $iconSets,
			'errors' => $errors,
		));
	}

	public function prepSettings($settings)
	{
			return $settings;
	}

	/**
	 * Returns the field's input HTML.
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @return string
	 */
	public function getInputHtml($name, $value)
	{
		if (!$value)
			$value = new SvgIconsModel();

		$settings = $this->getSettings();

		$id = craft()->templates->formatInputId($name);
		$namespacedId = craft()->templates->namespaceInputId($id);

		craft()->templates->includeCssResource('svgicons/css/fields/SvgIconsFieldType.css');
		craft()->templates->includeJsResource('svgicons/js/fields/SvgIconsFieldType.js');

		foreach (IOHelper::getFolderContents(craft()->path->getPluginsPath() . 'svgicons/resources/sprites', false, '\.svg.css$') as $sheet) {
			craft()->templates->includeCssResource('svgicons/sprites/' . IOHelper::getFilename($sheet));
		}

		$jsonVars = array(
			'id' => $id,
			'inputId' => craft()->templates->namespaceInputId($id),
			'name' => $name,
			'namespace' => $namespacedId,
			'prefix' => craft()->templates->namespaceInputId(''),
			'blank' => UrlHelper::getResourceUrl('svgicons/icon-blank.svg'),
			'iconSetUrl' => craft()->config->get('iconSetsUrl', 'svgicons'),
		);

		$jsonVars = json_encode($jsonVars);

		craft()->templates->includeJs('var svgIconsFieldType = new SvgIconsFieldType(' . $jsonVars . ');');

		$variables = array(
			'id' => $id,
			'name' => $name,
			'namespaceId' => $namespacedId,
			'values'  => $value,
			'options' => craft()->svgIcons->getIcons($settings->iconSets)
		);

		return craft()->templates->render('svgicons/fields/SvgIconsFieldType.twig', $variables);
	}

	/**
	 * Returns the input value as it should be saved to the database.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValueFromPost($value)
	{
		if ($value['icon'] == '_blank_') $value = null;

		if (substr($value['icon'], 0, strlen('svgicons-')) === 'svgicons-') {
			$value['sprite'] = str_replace('svgicons-', '', $value['icon']);
			$value['icon'] = null;
		}

		return $value;
	}

	/**
	 * Prepares the field's value for use.
	 *
	 * @param mixed $value
	 * @return mixed
	 */

	public function prepValue($value)
	{
		if(!$value) return null;

		$value = new SvgIconsModel($value);

		if ($value->sprite) {
			$value->icon = 'svgicons-' . $value->sprite;
		}

		list($value['width'], $value['height']) = craft()->svgIcons->getDimensions($value['icon']);

		return $value;
	}
}
