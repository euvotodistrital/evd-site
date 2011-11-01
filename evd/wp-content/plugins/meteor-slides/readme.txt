=== Plugin Name ===
Contributors: JLeuze
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mail%40jleuze%2ecom&item_name=Meteor%20Slides%20Donation&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: slide, slides, slider, slideshow, image, custom post types, jquery
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.2.3

Meteor Slides makes it simple to manage a slideshow with WordPress by adding a custom post type for slides.

== Description ==

This plugin makes it simple to manage a slideshow with WordPress by adding a custom post type for slides. The slideshow is powered by [jQuery Cycle](http://jquery.malsup.com/cycle/) and has over twenty transition styles to choose from.

The slides are managed as featured images through the media library; they will automatically be cropped to the dimensions specified on the settings page. Optionally, each slide can link to any Post, Page, or external URL of your choice.

= Features =

* **Easy integration:** Add the slideshow to your site using a template tag, shortcode, or widget.
* **Slideshow settings page:** Control the slide height and width, the number of slides, and the slideshow speed and transition style.
* **Slideshow transition styles:** blindX, blindY, blindZ, cover, curtainX, curtainY, fade, fadeZoom, growX, growY, none, scrollUp, scrollDown, scrollLeft, scrollRight, scrollHorz, scrollVert, slideX, slideY, turnUp, turnDown, turnLeft, turnRight, uncover, wipe, zoom.
* **Slideshow navigation:** Optional previous/next and paged slide navigation.
* **Multiple languages:** English, French, Indonesian, Italian, Portuguese, Romanian, Spanish, Turkish.

= Future Features =

* Multiple slideshows.
* Choose which slides display.
* Reorder slides.

[*Got an idea for a feature?*](http://wordpress.org/tags/meteor-slides?forum_id=10#postform "Post feedback or ideas in the forums")

== Installation ==

1. Upload the `meteor-slides` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use `<?php if(function_exists('meteor_slideshow')) { meteor_slideshow(); } ?>` to add this slideshow to your theme, use `[meteor_slideshow]` to add it to your Post or Page content, or use the Meteor Slides Widget to add it to a sidebar.

**Before adding any slides, go to the Meteor Slides Settings page and set the slide height and width so that those slides are cropped to the correct size.**

= Meteor Slides Screencast =
This short screencast explains how to set up Meteor Slides, create new slides, and add the slideshow to your site using the shortcode or template tag.

[vimeo http://vimeo.com/12901374]

*Please [post any questions or problems](http://wordpress.org/tags/meteor-slides?forum_id=10#postform "Post a question or problem in the forums") in the WordPress.org support forums.*

== Frequently Asked Questions ==

= I add a slide, save or publish it, and then it's missing or not found, what gives? =

Every post needs a title, make sure to give your slide a title where is says "Enter title here". This title is mostly used just to label them in the backend, but it will also be used as the title of your link if you add a link.

= I added an image to my post, why isn't it showing up in the slide? =

Make sure to click "Use as featured image" after uploading your image. If the image is added correctly to the slide, you could see a thumbnail of that image in the Slide Image metabox. 

= How can I switch the order of the slides? =

The slides load in the order they were published, you can change the publish date of a slide post to switch the order.

= Why is the slideshow covering up my dropdown menus? =

The `z-index` on the slideshow is higher than the dropdowns, causing them to be layered below the slides. Lower the `z-index` of `#meteor-slideshow` or raise the `z-index` of your dropdowns until the dropdowns are above the slideshow.

= How do I customize the slideshow's CSS stylesheet? =

Copy `meteor-slides.css` from `/meteor-slides/css/` to your theme's directory to replace the plugin's default stylesheet. If you have navigation enabled, be sure to copy the `buttons.png`, `next.png`, and `prev.png` files to your theme's images folder, or create new graphics to replace them.

= I installed Meteor Slides, and now my theme or plugin's jQuery goodies are broken! =

Your theme or plugin is probably loading an extra copy of jQuery from the theme or plugin, or a third party server. This is unnecessary because WordPress already uses jQuery and it is included in the WordPress install. Meteor Slides loads the version that is within WordPress, to fix this, fix your theme or plugin to use the copy of jQuery that comes with WordPress, like this `<?php wp_enqueue_script("jquery"); ?>`.

= I have another custom post type that uses featured images, and when I install Meteor Slides, it disables the featured image box in my custom post type, what's up with that? =

Meteor Slides is setup to check for post thumbnail support and create an array with the slides post type and any other post types that are in use. But in order for that to work, your theme or plugin needs to specify any custom post types in an array.

*Please [post any questions or problems](http://wordpress.org/tags/meteor-slides?forum_id=10#postform "Post a question or problem in the forums") in the WordPress.org support forums.*

== Screenshots ==

1. New Slide Page
2. Edit Slide Page
3. Meteor Slides Settings

== Changelog ==

= 1.2.3 =
* Centered slides, fixed navigation bugs, added slideshow navigation buttons, updated translation files.

= 1.2.2 =
* Added slideshow navigation, added Italian, Portuguese, and Spanish translations.

= 1.2.1 =
* Added French and Romanian translations.

= 1.2 =
* Added localization functionality, added Indonesian and Turkish translations.

= 1.1.1 =
* Fixed featured image array conflict with some themes, hide slides from revealing on load, added unique id to each slide.

= 1.1 =
* Added slideshow widget, added stylesheet, updated JQuery Cycle to 2.88.

= 1.0.2 =
* Fixed shortcode bugs, positioning of slideshow and loop within loop.

= 1.0.1 =
* Removed "menu_position" to prevent conflicts with other plugins.

= 1.0 =
* Initial release of Meteor Slides.

== Upgrade Notice ==

= 1.2.3 =
Meteor Slides 1.2.3 centers slides, fixes navigation bugs, adds slideshow navigation buttons, and updates translation files.

= 1.2.2 =
Meteor Slides 1.2.2 adds slideshow navigation and Italian, Portuguese, and Spanish translations.

= 1.2.1 =
Meteor Slides 1.2.1 adds French and Romanian translations.

= 1.2 =
Meteor Slides 1.2 adds localization support and includes Indonesian and Turkish translations.

= 1.1.1 =
This version of Meteor Slides fixes a bug that was causing some themes to disable the featured images.

= 1.1 =
This version of Meteor Slides adds a stylesheet for the slideshow which aids theme compatibility.