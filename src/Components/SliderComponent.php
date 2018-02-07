<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Slider\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-slider
 */

namespace SilverWare\Slider\Components;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\SS_List;
use SilverStripe\View\SSViewer;
use SilverWare\Colorpicker\Forms\ColorField;
use SilverWare\Components\BaseComponent;
use SilverWare\Extensions\Model\ImageResizeExtension;
use SilverWare\FontIcons\Forms\FontIconField;
use SilverWare\Forms\DimensionsField;
use SilverWare\Forms\FieldSection;
use SilverWare\Model\Slide;
use SilverWare\Tools\ImageTools;
use SilverWare\Tools\ViewTools;

/**
 * An extension of the base component class for a slider component.
 *
 * @package SilverWare\Slider\Components
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-slider
 */
class SliderComponent extends BaseComponent
{
    /**
     * Define corner constants.
     */
    const CORNER_ROUNDED  = 'rounded';
    const CORNER_CIRCULAR = 'circular';
    
    /**
     * Define mode constants.
     */
    const MODE_SLIDE = 'slide';
    const MODE_FADE  = 'fade';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Slider Component';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Slider Components';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which shows a series of slides';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/slider: admin/client/dist/images/icons/SliderComponent.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_SliderComponent';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = BaseComponent::class;
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = Slide::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Mode' => 'Varchar(16)',
        'Auto' => 'Boolean',
        'Loop' => 'Boolean',
        'Pause' => 'Int',
        'Speed' => 'Int',
        'NumberOfItems' => 'Int',
        'NumberOfThumbs' => 'Int',
        'PauseOnHover' => 'Boolean',
        'AdaptiveHeight' => 'Boolean',
        'ShowPager' => 'Boolean',
        'ShowThumbs' => 'Boolean',
        'ShowControls' => 'Boolean',
        'HideCaptionsOnMobile' => 'Boolean',
        'IconPrev' => 'FontIcon',
        'IconNext' => 'FontIcon',
        'ThumbResize' => 'Dimensions',
        'ThumbResizeMethod' => 'Varchar(32)',
        'ThumbCornerStyle' => 'Varchar(32)',
        'ColorPrimary' => 'Color',
        'ColorSecondary' => 'Color',
        'ColorControl' => 'Color',
        'ThumbMargin' => 'Int'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Mode' => 'slide',
        'Auto' => 1,
        'Loop' => 1,
        'Pause' => 8000,
        'Speed' => 1000,
        'NumberOfItems' => 1,
        'NumberOfThumbs' => 10,
        'PauseOnHover' => 1,
        'AdaptiveHeight' => 1,
        'ShowPager' => 1,
        'ShowThumbs' => 0,
        'ShowControls' => 1,
        'IconPrev' => 'chevron-left',
        'IconNext' => 'chevron-right',
        'ThumbResizeWidth' => 100,
        'ThumbResizeHeight' => 100,
        'ThumbResizeMethod' => 'fill-priority',
        'HideCaptionsOnMobile' => 1,
        'ThumbMargin' => 16
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        Slide::class
    ];
    
    /**
     * Maps field and method names to the class names of casting objects.
     *
     * @var array
     * @config
     */
    private static $casting = [
        'WrapperAttributesHTML' => 'HTMLFragment'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ImageResizeExtension::class
    ];
    
    /**
     * Defines the asset folder for uploading images.
     *
     * @var string
     * @config
     */
    private static $asset_folder = 'Slides/Slider';
    
    /**
     * Defines the tag to use for slides.
     *
     * @var string
     * @config
     */
    private static $slide_tag = 'li';
    
    /**
     * Holds a list of slides which override the child slides.
     *
     * @var SS_List
     */
    protected $slides;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Style Fields:
        
        $fields->addFieldsToTab(
            'Root.Style',
            [
                FieldSection::create(
                    'IconStyle',
                    $this->fieldLabel('Icons'),
                    [
                        FontIconField::create(
                            'IconPrev',
                            $this->fieldLabel('IconPrev')
                        ),
                        FontIconField::create(
                            'IconNext',
                            $this->fieldLabel('IconNext')
                        )
                    ]
                ),
                FieldSection::create(
                    'ThumbStyle',
                    $this->fieldLabel('Thumbnails'),
                    [
                        DimensionsField::create(
                            'ThumbResize',
                            $this->fieldLabel('ThumbResize')
                        ),
                        DropdownField::create(
                            'ThumbResizeMethod',
                            $this->fieldLabel('ThumbResizeMethod'),
                            ImageTools::singleton()->getResizeMethods()
                        ),
                        DropdownField::create(
                            'ThumbCornerStyle',
                            $this->owner->fieldLabel('ThumbCornerStyle'),
                            $this->getThumbCornerStyleOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        NumericField::create(
                            'ThumbMargin',
                            $this->fieldLabel('ThumbMargin')
                        )
                    ]
                ),
                FieldSection::create(
                    'ColorStyle',
                    $this->fieldLabel('Colors'),
                    [
                        ColorField::create(
                            'ColorPrimary',
                            $this->fieldLabel('ColorPrimary')
                        ),
                        ColorField::create(
                            'ColorSecondary',
                            $this->fieldLabel('ColorSecondary')
                        ),
                        ColorField::create(
                            'ColorControl',
                            $this->fieldLabel('ColorControl')
                        )
                    ]
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'SliderOptions',
                $this->fieldLabel('Slider'),
                [
                    NumericField::create(
                        'NumberOfItems',
                        $this->fieldLabel('NumberOfItems')
                    ),
                    DropdownField::create(
                        'Mode',
                        $this->fieldLabel('Mode'),
                        $this->getModeOptions()
                    ),
                    NumericField::create(
                        'Speed',
                        $this->fieldLabel('Speed')
                    ),
                    NumericField::create(
                        'Pause',
                        $this->fieldLabel('Pause')
                    ),
                    CheckboxField::create(
                        'Auto',
                        $this->fieldLabel('Auto')
                    ),
                    CheckboxField::create(
                        'Loop',
                        $this->fieldLabel('Loop')
                    ),
                    CheckboxField::create(
                        'PauseOnHover',
                        $this->fieldLabel('PauseOnHover')
                    ),
                    CheckboxField::create(
                        'AdaptiveHeight',
                        $this->fieldLabel('AdaptiveHeight')
                    ),
                    CheckboxField::create(
                        'HideCaptionsOnMobile',
                        $this->fieldLabel('HideCaptionsOnMobile')
                    ),
                    CheckboxField::create(
                        'ShowPager',
                        $this->fieldLabel('ShowPager')
                    ),
                    CheckboxField::create(
                        'ShowControls',
                        $this->fieldLabel('ShowControls')
                    ),
                    CheckboxField::create(
                        'ShowThumbs',
                        $this->fieldLabel('ShowThumbs')
                    ),
                    NumericField::create(
                        'NumberOfThumbs',
                        $this->fieldLabel('NumberOfThumbs')
                    )
                ]
            )
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['Mode'] = _t(__CLASS__ . '.MODE', 'Mode');
        $labels['Auto'] = _t(__CLASS__ . '.AUTO', 'Auto');
        $labels['Loop'] = _t(__CLASS__ . '.LOOP', 'Loop');
        $labels['Pause'] = _t(__CLASS__ . '.PAUSEINMS', 'Pause (in milliseconds)');
        $labels['Speed'] = _t(__CLASS__ . '.SPEEDINMS', 'Speed (in milliseconds)');
        $labels['Slider'] = _t(__CLASS__ . '.SLIDER', 'Slider');
        $labels['NumberOfItems'] = _t(__CLASS__ . '.NUMBEROFITEMS', 'Number of items');
        $labels['PauseOnHover'] = _t(__CLASS__ . '.PAUSEONHOVER', 'Pause on hover');
        $labels['AdaptiveHeight'] = _t(__CLASS__ . '.ADAPTIVEHEIGHT', 'Adaptive height');
        $labels['ShowPager'] = _t(__CLASS__ . '.SHOWPAGER', 'Show pager');
        $labels['ShowThumbs'] = _t(__CLASS__ . '.SHOWTHUMBNAILS', 'Show thumbnails');
        $labels['ShowControls'] = _t(__CLASS__ . '.SHOWCONTROLS', 'Show controls');
        $labels['NumberOfThumbs'] = _t(__CLASS__ . '.NUMBEROFTHUMBNAILS', 'Number of thumbnails');
        $labels['IconPrev'] = _t(__CLASS__ . '.PREVIOUSBUTTONICON', 'Previous button icon');
        $labels['IconNext'] = _t(__CLASS__ . '.NEXTBUTTONICON', 'Next button icon');
        $labels['ThumbResize'] = _t(__CLASS__ . '.DIMENSIONS', 'Dimensions');
        $labels['ThumbResizeMethod'] = _t(__CLASS__ . '.RESIZEMETHOD', 'Resize method');
        $labels['ThumbCornerStyle'] = _t(__CLASS__ . '.CORNERSTYLE', 'Corner style');
        $labels['Thumbnails'] = _t(__CLASS__ . '.THUMBNAILS', 'Thumbnails');
        $labels['Icons'] = _t(__CLASS__ . '.ICONS', 'Icons');
        $labels['Colors'] = _t(__CLASS__ . '.COLORS', 'Colors');
        $labels['ColorPrimary'] = _t(__CLASS__ . '.PRIMARYCOLOR', 'Primary color');
        $labels['ColorSecondary'] = _t(__CLASS__ . '.SECONDARYCOLOR', 'Secondary color');
        $labels['ColorControl'] = _t(__CLASS__ . '.CONTROLCOLOR', 'Control color');
        $labels['HideCaptionsOnMobile'] = _t(__CLASS__ . '.HIDECAPTIONSONMOBILE', 'Hide captions on mobile');
        $labels['ThumbMargin'] = _t(__CLASS__ . '.MARGININPX', 'Margin (in pixels)');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the asset folder used by the receiver.
     *
     * @return string
     */
    public function getAssetFolder()
    {
        return $this->config()->asset_folder;
    }
    
    /**
     * Defines the slides property for the receiver.
     *
     * @param SS_List $slides
     *
     * @return $this
     */
    public function setSlides(SS_List $slides)
    {
        $list = ArrayList::create();
        
        foreach ($slides as $slide) {
            $list->push($slide->setParentInstance($this));
        }
        
        $this->slides = $list;
    }
    
    /**
     * Answers a list of all slides within the receiver.
     *
     * @return DataList
     */
    public function getSlides()
    {
        return $this->slides ?: $this->getAllChildrenByClass(Slide::class);
    }
    
    /**
     * Answers true if the receiver has at least one slide.
     *
     * @return boolean
     */
    public function hasSlides()
    {
        return (boolean) $this->getSlides()->exists();
    }
    
    /**
     * Answers the tag for the slides.
     *
     * @return string
     */
    public function getSlideTag()
    {
        return $this->config()->slide_tag;
    }
    
    /**
     * Answers a list of the enabled slides within the receiver.
     *
     * @return ArrayList
     */
    public function getEnabledSlides()
    {
        $slides = ArrayList::create();
        
        foreach ($this->getSlides() as $slide) {
            $slides->merge($slide->getEnabledSlides());
        }
        
        return $slides;
    }
    
    /**
     * Answers an array of content class names for the HTML template.
     *
     * @return array
     */
    public function getContentClassNames()
    {
        $classes = parent::getContentClassNames();
        
        $classes[] = $this->ThumbCornerStyleClass;
        
        return $classes;
    }
    
    /**
     * Answers an array of HTML tag attributes for the wrapper.
     *
     * @return array
     */
    public function getWrapperAttributes()
    {
        $attributes = [
            'id' => $this->WrapperID,
            'class' => $this->WrapperClass
        ];
        
        $this->extend('updateWrapperAttributes', $attributes);
        
        $attributes = array_merge($attributes, $this->getWrapperDataAttributes());
        
        return $attributes;
    }
    
    /**
     * Answers an array of data attributes for the wrapper.
     *
     * @return array
     */
    public function getWrapperDataAttributes()
    {
        $attributes = [
            'data-mode' => $this->Mode,
            'data-item' => $this->NumberOfItems,
            'data-auto' => $this->dbObject('Auto')->NiceAsBoolean(),
            'data-loop' => $this->dbObject('Loop')->NiceAsBoolean(),
            'data-pause' => $this->Pause,
            'data-speed' => $this->Speed,
            'data-pager' => $this->dbObject('ShowPager')->NiceAsBoolean(),
            'data-gallery' => $this->dbObject('ShowThumbs')->NiceAsBoolean(),
            'data-controls' => $this->dbObject('ShowControls')->NiceAsBoolean(),
            'data-thumb-item' => $this->NumberOfThumbs,
            'data-pause-on-hover' => $this->dbObject('PauseOnHover')->NiceAsBoolean(),
            'data-adaptive-height' => $this->dbObject('AdaptiveHeight')->NiceAsBoolean(),
            'data-icon-prev' => $this->PreviousIcon,
            'data-icon-next' => $this->NextIcon,
            'data-gallery-margin' => $this->ThumbMargin
        ];
        
        $this->extend('updateWrapperDataAttributes', $attributes);
        
        return $attributes;
    }
    
    /**
     * Answers the HTML tag attributes for the wrapper as a string.
     *
     * @return string
     */
    public function getWrapperAttributesHTML()
    {
        return $this->getAttributesHTML($this->getWrapperAttributes());
    }
    
    /**
     * Answers an array of wrapper class names for the HTML template.
     *
     * @return array
     */
    public function getWrapperClassNames()
    {
        $classes = ['wrapper'];
        
        $this->extend('updateWrapperClassNames', $classes);
        
        return $classes;
    }
    
    /**
     * Answers a unique ID for the wrapper element.
     *
     * @return string
     */
    public function getWrapperID()
    {
        return sprintf('%s_Wrapper', $this->getHTMLID());
    }
    
    /**
     * Answers a unique CSS ID for the wrapper element.
     *
     * @return string
     */
    public function getWrapperCSSID()
    {
        return $this->getCSSID($this->getWrapperID());
    }
    
    /**
     * Answers an array of HTML tag attributes for the slide.
     *
     * @param Slide $slide
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return array
     */
    public function getSlideAttributes(Slide $slide, $isFirst = false, $isMiddle = false, $isLast = false)
    {
        $attributes = [];
        
        if ($slide->hasImage()) {
            
            $attributes['data-thumb'] = $slide->getImageResized(
                $this->ThumbResizeWidth,
                $this->ThumbResizeHeight,
                $this->ThumbResizeMethod
            )->URL;
            
        }
        
        return $attributes;
    }
    
    /**
     * Answers an array of slide class names for the HTML template.
     *
     * @param Slide $slide
     * @param boolean $isFirst Slide is first in the list.
     * @param boolean $isMiddle Slide is in the middle of the list.
     * @param boolean $isLast Slide is last in the list.
     *
     * @return array
     */
    public function getSlideClassNames(Slide $slide, $isFirst = false, $isMiddle = false, $isLast = false)
    {
        // Define Class Names:
        
        $classes[] = $this->style('slider.item');
        
        // Answer Class Names:
        
        return $classes;
    }
    
    /**
     * Answers an array of image class names for the HTML template.
     *
     * @param Slide $slide
     *
     * @return array
     */
    public function getImageClassNames(Slide $slide)
    {
        return $this->styles('image.fluid', 'slider.image');
    }
    
    /**
     * Answers an array of caption class names for the HTML template.
     *
     * @param Slide $slide
     *
     * @return array
     */
    public function getCaptionClassNames(Slide $slide)
    {
        $classes = $this->styles('slider.caption');
        
        if ($this->HideCaptionsOnMobile) {
            $classes[] = $this->style('slider.hide-mobile');
        }
        
        return $classes;
    }
    
    /**
     * Answers the name of the previous icon.
     *
     * @return string
     */
    public function getPreviousIcon()
    {
        return ($this->IconPrev) ? $this->IconPrev : 'chevron-left';
    }
    
    /**
     * Answers the name of the next icon.
     *
     * @return string
     */
    public function getNextIcon()
    {
        return ($this->IconNext) ? $this->IconNext : 'chevron-right';
    }
    
    /**
     * Answers the template used to render the receiver.
     *
     * @return string|array|SSViewer
     */
    public function getTemplate()
    {
        $viewer = new SSViewer(static::class);
        
        return $viewer->dontRewriteHashlinks();
    }
    
    /**
     * Answers true if the object is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->getEnabledSlides()->exists()) {
            return true;
        }
        
        return parent::isDisabled();
    }
    
    /**
     * Answers an array of options for the mode field.
     *
     * @return array
     */
    public function getModeOptions()
    {
        return [
            self::MODE_SLIDE => _t(__CLASS__ . '.SLIDE', 'Slide'),
            self::MODE_FADE  => _t(__CLASS__ . '.FADE', 'Fade'),
        ];
    }
    
    /**
     * Answers an array of options for the thumb corner style field.
     *
     * @return array
     */
    public function getThumbCornerStyleOptions()
    {
        return [
            self::CORNER_ROUNDED  => _t(__CLASS__ . '.ROUNDED', 'Rounded'),
            self::CORNER_CIRCULAR => _t(__CLASS__ . '.CIRCULAR', 'Circular'),
        ];
    }
    
    /**
     * Answers the corner style class for thumbnail images.
     *
     * @return string
     */
    public function getThumbCornerStyleClass()
    {
        switch ($this->ThumbCornerStyle) {
            case self::CORNER_ROUNDED:
                return 'rounded-thumbs';
            case self::CORNER_CIRCULAR:
                return 'circular-thumbs';
        }
    }
}
