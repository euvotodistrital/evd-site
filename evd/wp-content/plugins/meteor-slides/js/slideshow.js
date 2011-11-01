//Esse plugin foi modificado para o site http://euvotodistrital.org.br

// jQuery Cycle script which powers the Slideshow: http://jquery.malsup.com/cycle/

var $j = jQuery.noConflict();

var $slidespeed = parseInt( meteorslidessettings.meteorslideshowspeed );

var $slidetimeout = parseInt( meteorslidessettings.meteorslideshowduration );

var $slideheight = parseInt( meteorslidessettings.meteorslideshowheight );

var $slidewidth = parseInt( meteorslidessettings.meteorslideshowwidth );

var $slidetransition = meteorslidessettings.meteorslideshowtransition;

$j(document).ready(function() {

    $j('.slides').cycle({
      height: $slideheight,
      width: $slidewidth,
      fit: '1',
      fx: $slidetransition,
      speed: $slidespeed,
      timeout: $slidetimeout,
      pause: '1',
      prev:   '#prev',
      next:   '#next',
      pager:  '#meteor-buttons',
      pagerEvent: 'click'
	});
	
	$j('.meteor-slides,#meteor-nav a,.slides').css('height', $slideheight);
		
	$j('.meteor-slides,.slide,#meteor-nav').css('width', $slidewidth);
	
	$j('.slides').css('overflow', 'visible');
});