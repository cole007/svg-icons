# SVG Icons plugin for Craft CMS

Easily access urls or inline SVG icons from a public directory.

![Screenshot](resources/screenshots/plugin_logo.png)

## Installation

To install SVG Icons, follow these steps:

1. Download & unzip the file and place the `svgicons` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/fyrebase/svg-icons.git svgicons` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3. Install plugin in the Craft Control Panel under Settings > Plugins
4. The plugin folder should be named `svgicons` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

SVG Icons works on Craft 2.6.x.

## SVG Icons Overview

The SVG Icons plugin introduces a custom fieldtype allowing you to quickly and easily access any set of SVG icons stored within your sites public directory without giving your clients the ability to delete or upload.

![Screenshot](resources/screenshots/svg-icon-fieldtype.png)

To get started, simply create a directory called `svgicons` in your `public` directory and place a subdirectory containing your SVG icons within. You can add as many icon sets as you like.

Create a field using the `SVG Icons` field type and choose which icon set you would like to use.

---

**ENSURE SVG DOES NOT CONTAIN WIDTH & HEIGHT ATTRIBUTES IF YOU WOULD LIKE TO SCALE ICON WITH CSS**

---

## Configuring SVG Icons

SVG Icons comes with its own `config.php` which you can over ride by simply creating a `svgicons.php` in your Craft `config` directory.

### iconSetsPath [string]

***Default `$_SERVER['DOCUMENT_ROOT'] . '/svgicons/'`***

File system path to the folder where you want to store your icon sets.

### iconSetsUrl [string]

***Default `'/svgicons/'`***

The `iconSetsUrl` will be prepended to the path and filename of the icon.

## Using SVG Icons

### SvgIconsModel

Public attributes and methods of the `SvgIconsModel` model.

#### Attributes

**`icon` [string]**

The icon set path including icon file name

**`width` [number]**

The icon original width

**`height` [number]**

The icon original height

#### Methods

#### \__toString

**`{{ svgIcon }}` [string]**

Returns icon public url


##### getInline

**`{{ svgIcon.inline }}` [string]**

Returns inlines icon

Example `{{ svgIcon }}`

##### getUrl

**`{{ svgIcon.url }}` [string]**

Returns icon public URL

Example `{{ svgIcon.url }}`

##### getDimensions

**`{{ svgIcon.dimensions }}` [array]**

Returns icon dimensions as array

Example `{{ svgIcon.dimensions }}`

##### setDimensions

**`{{ svgIcon.setDimensions(newHeight) }}` [array]**

Returns icon new dimensions in pixels as array maintaining aspect ratio

Example `{{ svgIcon.setDimensions(24) }}`

### Template Variables

#### Get SVG Icon Model From String

**`{{ craft.svgIcons.getModel(path) }}` [model]**

Returns icon data as array

Example `{{ craft.svgIcons.getModel('fontawesome/align-left.svg') }}`

#### Inline SVG Icon

**`{{ craft.svgIcons.inline(path) }}` [string]**

Returns icon public URL

Example `{{ craft.svgIcons.inline('fontawesome/align-left.svg') }}`

#### Get SVG Icon Dimensions

**`{{ craft.svgIcons.getDimensions(path) }}` [array]**

Returns icon dimensions as array

Example `{{ craft.svgIcons.getDimensions('fontawesome/align-left.svg') }}`

#### Set SVG Icon Dimensions

**`{{ craft.svgIcons.setDimensions(path, newHeight) }}` [array]**

Returns icon new dimensions in pixels as array maintaining aspect ratio

Example `{{ craft.svgIcons.setDimensions('fontawesome/align-left.svg', 24) }}`

## SVG Icons Roadmap

Some things to do, and ideas for potential features:

* Release it

## Bugs and Suggestions

If you stumble across any bugs let me know, or better yet submit a pull request!

I'm open to feed back and suggestions as I'm sure there is plenty of room for improvement.

## SVG Icons Changelog

### 0.0.1 -- 2016.09.13

* **[Added]** Obtain icon model from string using new template variable `getModel`
* **[Added]** Renamed `getIconFromString` service method to `getModel` and fixed pathing issue
* **[Improved]** Updated README.md

### 0.0.1 -- 2016.09.13

* Initial release

Brought to you by [Fyrebase](http://fyrebase.com)
