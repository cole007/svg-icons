<?php
/**
 * SVG Icons plugin for Craft CMS
 *
 * SvgIcons FieldType
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

class SvgIconsService extends BaseApplicationComponent
{
	/**
	 * Return icon sets select options
	 * @param  mixed $iconSets
	 * @return array
	 */
	public function getIcons($iconSets) {

		$icons = array();

		if ($iconSets == '*') {
			foreach (IOHelper::getFolders(craft()->config->get('iconSetsPath', 'svgicons')) as $folder) {
				$icons[] = array('optgroup' => IOHelper::getFolderName($folder, false));
				$icons = array_merge($icons, $this->_getIcons($folder));
			}
		} else {
			foreach ($iconSets as $folder) {
				$icons[] = array('optgroup' => IOHelper::getFolderName($folder, false));
				$icons = array_merge($icons, $this->_getIcons($folder));
			}
		}

		return $icons;
	}

	/**
	 * Return icon array from string
	 * @param  string $icon
	 * @return array
	 */
	public function getIconFromString($icon)
	{
		if (!IoHelper::fileExists(craft()->config->get('iconSetsPath', 'svgicons') . $icon)) return false;

		$iconPath = craft()->config->get('iconSetsUrl', 'svgicons') . $icon;

		list($width, $height) = $this->getDimensions($icon);

		return array(
			'icon' => $iconPath,
			'width' => $width,
			'height' => $height,
		);
	}

	/**
	 * Return icon dimensions
	 * @param  string $icon
	 * @return array
	 */
	public function getDimensions($icon)
	{
		return ImageHelper::getImageSize(craft()->config->get('iconSetsPath', 'svgicons') . $icon);
	}

	/**
	 * Set icon dimensions maintaining aspect ratio
	 * @param string $icon
	 * @param int $baseHeight
	 */
	public function setDimensions($icon, $baseHeight)
	{
		if($icon instanceof SvgIconsModel) {
			$w = $icon->width;
			$h = $icon->height;
		} else {
			list($w, $h) = ImageHelper::getImageSize(craft()->config->get('iconSetsPath', 'svgicons') . $icon);
		}

		return array(
			'width' => ceil(($w / $h) * $baseHeight),
			'height' => $baseHeight
		);
	}

	/**
	 * Return icon file contents
	 * ready for raw output
	 * @param  string $icon
	 * @return string
	 */
	public function inline($icon)
	{
		$path = craft()->config->get('iconSetsPath', 'svgicons') . $icon;

		if (!IoHelper::fileExists($path)) return '';

		return TemplateHelper::getRaw(@file_get_contents($path));
	}

	/**
	 * Return icon set select options
	 * @param  string $folder
	 * @return array
	 */
	private function _getIcons($folder) {
		$d = IOHelper::getFolderContents($folder, false);

		$icons = array();

		if (is_array($d)) {
			foreach ($d as $i)
			{
				$icons[] = array('value' => IOHelper::getFolderName($folder, false) . DIRECTORY_SEPARATOR . IOHelper::getFileName($i), 'label' => IOHelper::getFileName($i, false));
			}
		}

		return $icons;
	}
}
