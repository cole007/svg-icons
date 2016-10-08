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
	private $sprites = array();

	public function init()
	{
		foreach (IOHelper::getFolderContents(craft()->path->getPluginsPath() . 'svgicons/resources/sprites', false, '\.json$') as $json) {
			$this->sprites = array_merge(JsonHelper::decode(IOHelper::getFileContents($json)), $this->sprites);
		}

		parent::init();
	}

	/**
	 * Return icon sets select options
	 * @param  mixed $iconSets
	 * @return array
	 */
	public function getIcons($iconSets) {

		$icons = array();

		if ($iconSets == '*') {
			foreach (IOHelper::getFolders(craft()->config->get('iconSetsPath', 'svgicons')) as $folder) {
				$icons = array_merge($icons, $this->_getOptions($folder));
			}
		} else {
			foreach ($iconSets as $folder) {
				$icons = array_merge($icons, $this->_getOptions($folder));
			}
		}

		return $icons;
	}

	/**
	 * Return icon model from string
	 * @param  string $icon
	 * @return array
	 */
	public function getModel($icon)
	{
		if (!IoHelper::fileExists(craft()->config->get('iconSetsPath', 'svgicons') . $icon)) return false;

		$model = new SvgIconsModel($icon);

		$model->icon = $icon;
		list($model->width, $model->height) = $this->getDimensions($icon);

		return $model;
	}

	/**
	 * Return icon dimensions
	 * @param  string $icon
	 * @return array
	 */
	public function getDimensions($icon)
	{
		if (isset($this->sprites[$icon]) || substr($icon, 0, strlen('svgicons-')) === 'svgicons-') {
			$icon = str_replace('svgicons-', '', $icon);
			return array_values($this->sprites[$icon]);
		}

		return IOHelper::getFile(craft()->config->get('iconSetsPath', 'svgicons') . $icon) ? ImageHelper::getImageSize(craft()->config->get('iconSetsPath', 'svgicons') . $icon) : array(0,0);
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
			if (substr($icon, 0, strlen('svgicons-')) === 'svgicons-') {
				$sprite = str_replace('svgicons-', '', $icon);
				list($w, $h) = $this->sprites[$sprite];
			} else {
				list($w, $h) = ImageHelper::getImageSize(craft()->config->get('iconSetsPath', 'svgicons') . $icon);
			}
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
	 * Return icon file name
	 * @param  string $icon
	 * @return string
	 */
	public function getFilename($icon)
	{
		return IOHelper::getFileName(craft()->config->get('iconSetsUrl', 'svgicons') . $icon, false);
	}

	/**
	 * Generate sprite sheet resources
	 * @param  string $stylesheet
	 */
	public function getSprites($stylesheet)
	{
		$filename = IOHelper::getFileName($stylesheet, false);

		$oCssParser = new \Sabberworm\CSS\Parser(IOHelper::getFileContents($stylesheet));
		$oCss = $oCssParser->parse();

		$classes = array();

		// Namespace css class
		// Store width / height
		foreach($oCss->getAllDeclarationBlocks() as $oBlock) {
			$class = str_replace('.', '', $oBlock->getSelector()[0]->getSelector());
			$oBlock->getSelector()[0]->setSelector(str_replace('.', '.svgicons-', $oBlock->getSelector()[0]->getSelector()));
			$classes[$class] = array();
			foreach ($oBlock->getRules() as $rule) {
				if ($rule->getRule() == 'width') {
					$classes[$class]['width'] = $rule->getValue()->getSize();
				}

				if ($rule->getRule() == 'height') {
					$classes[$class]['height'] = $rule->getValue()->getSize();
				}
			}
		}

		// Remove redundant rules
		foreach($oCss->getAllRuleSets() as $oRuleSet) {
			$oRuleSet->removeRule('width');
			$oRuleSet->removeRule('height');
		}

		IOHelper::writeToFile(craft()->path->getPluginsPath().'svgicons/resources/sprites/' . $filename . '.css', $oCss->render());
		IOHelper::writeToFile(craft()->path->getPluginsPath().'svgicons/resources/sprites/' . $filename . '.json', JsonHelper::encode($classes));
	}

	/**
	 * Return icon set select options
	 * @param  string $folder
	 * @return array
	 */
	private function _getOptions($folder) {
		if (IOHelper::getFolderContents($folder, false, '\.svg.css')) {
			$sheet = IOHelper::getFolderContents(craft()->path->getPluginsPath() . 'svgicons/resources/sprites', false, '\.json$')[0];
			$icons[] = array('optgroup' => str_replace('.svg', '', IOHelper::getFileName($sheet, false)));
			$icons = array_merge($icons, $this->_getOptionsFromJson($sheet));
		} else {
			$icons[] = array('optgroup' => IOHelper::getFolderName($folder, false));
			$icons = array_merge($icons, $this->_getOptionsFromFile($folder));
		}

		return $icons;
	}

	/**
	 * Return icon set select options
	 * from svg file name
	 * @param  string $folder
	 * @return array
	 */
	private function _getOptionsFromFile($folder) {
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

	/**
	 * Return icon set select options
	 * from json
	 * @param  string $jsonFile
	 * @return array
	 */
	private function _getOptionsFromJson($jsonFile) {
		$d = JsonHelper::decode(IOHelper::getFileContents($jsonFile));

		$icons = array();

		if (is_array($d)) {
			foreach ($d as $k => $v)
			{
				$icons[] = array('value' => 'svgicons-' . $k, 'label' => $k);
			}
		}

		return $icons;
	}
}
