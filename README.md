# SilverWare Slider Module

[![Latest Stable Version](https://poser.pugx.org/silverware/slider/v/stable)](https://packagist.org/packages/silverware/slider)
[![Latest Unstable Version](https://poser.pugx.org/silverware/slider/v/unstable)](https://packagist.org/packages/silverware/slider)
[![License](https://poser.pugx.org/silverware/slider/license)](https://packagist.org/packages/silverware/slider)

Provides a slider component for use with [SilverWare][silverware].

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Issues](#issues)
- [Contribution](#contribution)
- [Attribution](#attribution)
- [Maintainers](#maintainers)
- [License](#license)

## Requirements

- [SilverWare][silverware]

## Installation

Installation is via [Composer][composer]:

```
$ composer require silverware/slider
```

## Usage

This module provides a `SliderComponent` which can be added to your SilverWare templates or
layouts using the CMS.

### Slider Component

The `SliderComponent` represents an instance of [jQuery lightSlider][lightslider]. Slides
may added to the slider by adding either a slide or a list source to the slider using the CMS.

The dimensions and resize method used for slides is configured using the Style tab of the component.
You may also select the font icons to use for the next and previous controls, the dimensions and
resize method used for slide thumbnails (if enabled), along with custom colors on the same tab.

On the Options tab, you can define the slide speed and pause in milliseconds, the slide transition
mode, adaptive height, pause on hover, and a number of other settings. If the "Show thumbnails" option
is checked, a series of small thumbnails will appear under the slider for each slide.

### Slides

Slides are added as children of the component using the site tree. Each slide may have an image,
caption, and a link to either a page within the CMS, or a URL. Slides will appear in the order defined
within the site tree.

### List Sources

A special type of slide is a "List Source Slide". This type of slide allows you to choose a list source
from within your CMS (e.g. blog, gallery etc.) and render the list items as slides within the slider.
Each item object must make use of the SilverWare `MetaDataExtension` and the `ListItem` trait.

## Issues

Please use the [GitHub issue tracker][issues] for bug reports and feature requests.

## Contribution

Your contributions are gladly welcomed to help make this project better.
Please see [contributing](CONTRIBUTING.md) for more information.

## Attribution

- Makes use of [jQuery lightSlider][lightslider] by [Sachin N](https://github.com/sachinchoolur).

## Maintainers

[![Colin Tucker](https://avatars3.githubusercontent.com/u/1853705?s=144)](https://github.com/colintucker) | [![Praxis Interactive](https://avatars2.githubusercontent.com/u/1782612?s=144)](https://www.praxis.net.au)
---|---
[Colin Tucker](https://github.com/colintucker) | [Praxis Interactive](https://www.praxis.net.au)

## License

[BSD-3-Clause](LICENSE.md) &copy; Praxis Interactive

[silverware]: https://github.com/praxisnetau/silverware
[composer]: https://getcomposer.org
[lightslider]: http://sachinchoolur.github.io/lightslider
[issues]: https://github.com/praxisnetau/silverware-slider/issues
