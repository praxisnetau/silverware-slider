/* Slider Component
===================================================================================================================== */

import $ from 'jquery';
import 'lightslider';

$(function() {
  
  $('.slidercomponent').each(function() {
    
    var $wrapper = $(this).find('.wrapper');
    
    $wrapper.lightSlider({
      item: $wrapper.data('item'),
      auto: $wrapper.data('auto'),
      loop: $wrapper.data('loop'),
      speed: $wrapper.data('speed'),
      pause: $wrapper.data('pause'),
      mode: $wrapper.data('mode'),
      pager: $wrapper.data('pager'),
      adaptiveHeight: $wrapper.data('adaptive-height'),
      pauseOnHover: $wrapper.data('pause-on-hover'),
      gallery: $wrapper.data('gallery'),
      controls: $wrapper.data('controls'),
      thumbItem: $wrapper.data('thumb-item'),
      galleryMargin: parseInt($wrapper.data('gallery-margin')),
      prevHtml: '<i class="fa fa-' + $wrapper.data('icon-prev') + '"></i>',
      nextHtml: '<i class="fa fa-' + $wrapper.data('icon-next') + '"></i>'
    });
    
  });
  
});
