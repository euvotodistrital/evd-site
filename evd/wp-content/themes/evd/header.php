<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
  <title><?php wp_title('- ', true, 'right'); bloginfo('name'); ?></title>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <?php /*
  <?php $description = have_posts() && get_post($id = 0)->post_excerpt ? get_the_excerpt() : get_bloginfo('description'); ?>
  <meta name="description" content="<?php echo $description; ?>" />
  <meta name="keywords" content="distritão, voto distrital, voto distrital misto, voto distrital puro, lista fechada, sistema eleitoral" />
  */ ?>
  <meta name="author" content="Jogo12" />
  <?php if (is_home()) : ?>
  <meta property="og:image" content="<?php bloginfo('template_directory'); ?>/images/logo.gif" />
  <?php endif; ?>
  <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="all" />
  <link rel="shortcut icon" type="image/png" href="<?php echo home_url('favicon.png'); ?>" />
  <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" /> 
  <link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" /> 
  <link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/stylesheets/style-ie.css" media="all" />
  <![endif]-->
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" charset="utf-8"></script>
  <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascripts/evd.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascripts/jquery.tipsy.js" charset="utf-8"></script>
  <?php if (is_page('assinaturas')) { ?>
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascripts/multiplesign.js" charset="utf-8"></script>
  <?php } ?>
  <?php wp_head(); ?>
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-22305241-1']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
</head>
<body <?php body_class($class); ?>>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
  <div id="page">
    <header>
      <?php $h_level = is_home() || is_page('blog') ? 'h1' : 'h2'; ?>
      <<?php echo $h_level; ?>><a href="<?php echo home_url(); ?>" title="Ir para a páginia inicial"><?php bloginfo('name'); ?></a></<?php echo $h_level; ?>>
      <p class="hide">Mais poder ao cidadão</p>
      <?php wp_nav_menu(array('menu' => 'primary', 'container' => 'nav', 'container_class' => 'primary')); ?>
      
      <?php evd_social_bar(); ?>
    </header>
