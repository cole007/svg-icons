<?php
/**
 * SVG Icons plugin for Craft CMS
 *
 * SvgIcons Model
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

class SvgIconsModel extends BaseModel
{
	/**
	 * Defines this model's attributes.
	 *
	 * @return array_merge
	 */
	protected function defineAttributes()
	{
		return array_merge(parent::defineAttributes(), array(
			'icon' => array(AttributeType::String, 'default' => ''),
			'sprite' => array(AttributeType::String, 'default' => ''),
			'width' => array(AttributeType::Number, 'default' => ''),
			'height' => array(AttributeType::Number, 'default' => ''),
		));
	}

	/**
	 * @return string the URL to the image
	 */
	public function __toString()
	{
		return $this->getUrl();
	}

	/**
	 * Return new icon dimensions maintaining aspect ratio
	 * @param int $baseHeight
	 */
	public function setDimensions($baseHeight)
	{
		return craft()->svgIcons->setDimensions($this, $baseHeight);
	}

	/**
	 * Return icon dimensions
	 * @return array
	 */
	public function getDimensions()
	{
		return craft()->svgIcons->getDimensions($this->icon);
	}

	/**
	 * Return icon public url
	 * @return string
	 */
	public function getUrl()
	{
		return craft()->config->get('iconSetsUrl', 'svgicons') . $this->icon;
	}

	/**
	 * Return icon filename
	 * @return string
	 */
	public function getFilename()
	{
		return craft()->svgIcons->getFilename($this->icon);
	}

	/**
	 * Return inline icon
	 * @return string
	 */
	public function getInline()
	{
		return TemplateHelper::getRaw(craft()->svgIcons->inline($this->icon));
	}

}
