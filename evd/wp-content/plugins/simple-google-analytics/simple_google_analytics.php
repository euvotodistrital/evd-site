<?php
/*
Plugin Name: Simple Google Analytics
Plugin URI: http://www.arobase62.fr/2011/03/23/simple-google-analytics/
Description: A simple Plugin to add Analytics code on your pages. You simply enter your ID, you choose if you are on a sub-domain and add your domain name.
Version: 1.0
Author: Jerome Meyer
Author URI: http://www.arobase62.fr
*/
?>
<?php

	// On lance la traduction
	add_action('init', 'launch_i18n') ;

	// On lance la fonction 'sga_menu' lorsque le menu est chargé
	add_action('admin_menu', 'sga_menu') ;
	
	
	// Fonction de traduction
	function launch_i18n() {
		$lngPath = basename(dirname(__FILE__)) . '/languages' ;
		load_plugin_textdomain('simple_google_analytics', false, $lngPath) ;
	}
	
	// Ajoute un sous-menu aux réglages
	function sga_menu() {
		// Génère le lien vers la page de configuration, seulement pour les administrateurs, et lance la fonction 'sga_settings' une fois le menu chargé
		add_options_page('Simple Google Analytics', 'Simple Google Analytics Settings', 'administrator', __FILE__, 'sga_settings') ;
		// On lance la fonction au moment où l'on accède à la zone d'admin
		add_action( 'admin_init', 'register_mysettings') ;
	}	
	
	// On enregistre les paramètres spécifiques au plugin
	function register_mysettings() {
		register_setting('sga-settings-group', 'analytics_id') ;
		register_setting('sga-settings-group', 'multidomain_setting') ;
		register_setting('sga-settings-group', 'multidomain_domain') ;
	}
	
	// Affichage de la page de paramètres
	function sga_settings() {

	?>
		<div class="wrap">
			<script>
				(function($) {
					$(document).ready(function() {
						// On active ou desactive le champs 'domain' selon notre choix sous-domaine ou pas
						$('#multisetting').bind('change', function() {
							if ($(this).val() == 1) {
								$('#multidomain').removeAttr('disabled') ;
							} else {
								$('#multidomain').attr('disabled', 'disabled') ;
							}
						}) ;
						
						// On lance une alerte et on stop la validation si on est en multi sous-domaine et que le domaine est vide
						$('#google_form').bind('submit', function() {
							if ($('#multisetting').val() == 1 && !$('#multidomain').val()) {
								alert('Domain empty') ;
								return false ;
							}
						}) ;
					}) ;
				})(jQuery) ;
			</script>
			<h2><?php _e('Simple Google Analytics.', 'simple_google_analytics') ; ?></h2>
			<p>
				<?php
					_e('Simple Google Analytics allows you to easilly add your Google Analytics code on all your pages.', 'simple_google_analytics') ;
					echo '<br/>' ;
					_e('Just add your ID, choose if you are on a sub-domain (setting in Google Analytics code), and enter the domain.', 'simple_google_analytics') ;
					echo '<br/>' ;
					_e('That\'s all, you\'re ready to go.', 'simple_google_analytics') ;
				?>
			</p>
	
			<form method="post" action="options.php" id="google_form">
				<?php settings_fields('sga-settings-group'); ?>
		
				<table class="form-table">
					<tr valign="top">
						<th scope="row" style="width: 300px; text-align:right;"><?php _e('Google Analytics ID', 'simple_google_analytics') ; ?></th>
						<td>
							<input type="text" name="analytics_id" value="<?php echo get_option('analytics_id'); ?>"> <?php _e('Example : UA-0000000-0.', 'simple_google_analytics') ; ?>
						</td>
					</tr>
					<tr>
						<th scope="row" style="text-align:right;" valign="top"><?php _e('Is your blog a sub-domain ? (Google Analytics Setting)', 'simple_google_analytics') ; ?></th>
						<td>
							<select id="multisetting" name="multidomain_setting">
								<option value="0" <?php selected(get_option('multidomain_setting'), 0) ; ?>><?php _e('No', 'simple_google_analytics') ; ?></option>
								<option value="1" <?php selected(get_option('multidomain_setting'), 1) ; ?>><?php _e('Yes', 'simple_google_analytics') ; ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="text-align:right;"><?php _e('Domain', 'simple_google_analytics') ; ?></th>
						<td>
							<input type="text" id="multidomain" name="multidomain_domain" value="<?php echo get_option('multidomain_domain'); ?>" <?php disabled(get_option('multidomain_setting'), 0) ;?>> <?php _e('Example : domain.com', 'simple_google_analytics') ; ?>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		
			<p>
				<?php
					_e('If you need any support go to the plugin homepage and contact me !', 'simple_google_analytics') ;
					echo '<br/><br/>' ;
					echo '<a href="http://www.arobase62.fr/2011/03/23/simple-google-analytics/" target="_blank">http://www.arobase62.fr/2011/03/23/simple-google-analytics/</a>' ;
					echo '<br/><br/>' ;
					_e('This plugin is largely inspired by the Google Analytics Input plugin from Roy Duff ( http://wpable.com ).', 'simple_google_analytics') ;
					echo '<br/><br/>' ;
					_e('If you like this plugin, you can Donate :)', 'simple_google_analytics') ;
					echo '<br/>' ;
				?>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/fr_FR/i/scr/pixel.gif" width="1" height="1">
				</form>

			</p>
		</div>

	<?php
	}
	
	// Action à lancer lorsque l'on affiche le footer de chaque page
	add_action('wp_footer', 'sga_function');

	// Le texte à rajouter dans le footer
	function sga_function() {
		// Seulement si l'utilisateur n'est pas loggé !
		if ( !is_user_logged_in() ) { ?>
	
			<script type="text/javascript">

				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', "<?php echo get_option('analytics_id'); ?>"]);
				<?php if (get_option('multidomain_setting') == 1) { ?>
				_gaq.push(['_setDomainName', "<?php echo get_option('multidomain_domain'); ?>"]);
				<?php } ?>
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();

			</script>

		<?php 
		}
	}
?>
