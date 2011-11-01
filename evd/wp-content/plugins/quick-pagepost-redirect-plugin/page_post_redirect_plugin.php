<?php 
/*
Plugin Name: Quick Page/Post Redirect DEV
Plugin URI: http://www.fischercreativemedia.com/wordpress-plugins/quick-pagepost-redirect-plugin/
Description: Redirect Pages, Posts or Custom Post Types to another location quickly (for internal or external URLs). Includes individual post/page options, redirects for Custom Post types, non-existant 301 Quick Redirects (helpful for sites converted to WordPress), New Window functionality, and rel=nofollow functionality.
Author: Don Fischer
Author URI: http://www.fischercreativemedia.com/
Donate link: http://www.fischercreativemedia.com/wordpress-plugins/donate/
Version: 4.2.2 

Version info:
See change log in readme.txt file.
Version 3.2.4 to 4.0.1 are testing versions only

    Copyright (C) 2009-2011 Donald J. Fischer

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	
	if (!function_exists('esc_attr')) {
	// For WordPress < 2.8 function compatibility
		function esc_attr($attr){return attribute_escape( $attr );}
		function esc_url($url){return clean_url( $url );}
	}

//=======================================
// Main Redirect Class.
//=======================================
class quick_page_post_reds {
	public $ppr_nofollow;
	public $ppr_newindow;
	public $ppr_url;
	public $ppr_url_rewrite;
	public $ppr_type;
	public $ppr_curr_version;
	public $ppr_metaurlnew;
	public $thepprversion;
	public $thepprmeta;
	public $quickppr_redirects;
	public $tohash;
	public $fcmlink;
	public $adminlink;
	public $ppr_all_redir_array;
	public $homelink;
	public $updatemsg;
	public $pproverride_nofollow;
	public $pproverride_newwin;
	public $pproverride_type;
	public $pproverride_active;
	public $pproverride_URL;
	public $pproverride_rewrite;
	public $pprmeta_seconds;
	public $pprmeta_message;
	
	function __construct() {
		$this->ppr_curr_version 	= '4.1';
		$this->ppr_nofollow 		= array();
		$this->ppr_newindow 		= array();
		$this->ppr_url 				= array();
		$this->ppr_url_rewrite 		= array();
		$this->thepprversion 		= get_option('ppr_version');
		$this->thepprmeta 			= get_option('ppr_meta_clean');
		$this->quickppr_redirects 	= get_option('quickppr_redirects');
		$this->homelink 			= get_option('home');
		$this->pproverride_nofollow = get_option( 'ppr_override-nofollow' );
		$this->pproverride_newwin 	= get_option( 'ppr_override-newwindow' );
		$this->pproverride_type 	= get_option( 'ppr_override-redirect-type' );
		$this->pproverride_active 	= get_option( 'ppr_override-active' );
		$this->pproverride_URL 		= get_option( 'ppr_override-URL' );
		$this->pproverride_rewrite	= get_option( 'ppr_override-rewrite' );
		$this->pprmeta_message		= get_option( 'ppr_meta-message' );
		$this->pprmeta_seconds		= get_option( 'ppr_meta-seconds' );
		$this->adminlink 			= get_bloginfo('url').'/wp-admin/';
		$this->fcmlink				= 'http://www.fischercreativemedia.com/wordpress-plugins';
		$this->ppr_metaurl			= '';
		$this->ppr_all_redir_array	= $this->get_main_array();
		$this->updatemsg			= '';
		if($this->pprmeta_seconds==''){$this->pprmeta_seconds='0';}
		
		//these are for all the time - even if there are overrides
		add_action( 'init', array($this,'ppr_init_check_version'), 1 );				// checks version of plugin in DB and updates if needed.
		add_action(	'save_post', array($this,'ppr_save_metadata'),20,2 ); 			// save the custom fields
	  	add_action( 'wp',array($this,'ppr_parse_request') );						// parse query vars
		add_action( 'admin_menu', array($this,'ppr_add_menu') ); 					// add the menu items needed
		add_action( 'admin_menu', array($this,'ppr_add_metabox') ); 				// add the metaboxes where needed
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array($this,'ppr_filter_plugin_actions') );
		add_filter( 'query_vars',array($this,'ppr_queryhook') ); 
		add_filter( 'plugin_row_meta',  array($this,'ppr_filter_plugin_links'), 10, 2 );
		
		if($this->pproverride_active!='1' && !is_admin()){ 								// don't run these if override active is set
			add_action( 'init', array($this,'redirect'), 1 ); 							// add the 301 redirect action, high priority
			add_action( 'init', array($this,'redirect_post_type'), 1 ); 				// add the normal redirect action, high priority
			add_action( 'template_redirect',array($this,'ppr_do_redirect'), 1, 2);		// do the redirects
			add_filter( 'wp_get_nav_menu_items',array($this,'ppr_new_nav_menu_fix'),1,1 );
			add_filter( 'wp_list_pages',array($this,'ppr_fix_targetsandrels') );
			add_filter( 'page_link',array($this,'ppr_filter_page_links'),20, 2 );
			add_filter( 'post_link',array($this,'ppr_filter_page_links'),20, 2 );
			add_filter( 'post_type_link', array($this, 'ppr_filter_page_links'), 20, 2 );
			add_filter( 'get_permalink', array($this, 'ppr_filter_links'), 20, 2 );
		}
		if (isset( $_POST['submit_301']) ) {$this->quickppr_redirects = $this->save_redirects($_POST['quickppr_redirects']);$this->updatemsg ='Quick Redirects Updated.';} //if submitted, process the data
		if (isset( $_REQUEST['settings-updated'] ) &&  $_REQUEST['settings-updated']=='true'){$this->updatemsg ='Settings Updated.';}
	}
	
	function ppr_add_menu(){
		add_menu_page('Redirect Menu', 'Redirect Menu', 'administrator', 'redirect-options', array($this,'ppr_settings_page'),WP_PLUGIN_URL.'/quick-pagepost-redirect-plugin/settings-16-icon.png');
		add_submenu_page( 'redirect-options', 'Quick Redirects', 'Quick Redirects', 'manage_options', 'redirect-updates', array($this,'ppr_options_page') );
		add_submenu_page( 'redirect-options', 'Redirect Summary', 'Redirect Summary', 'manage_options', 'redirect-summary', array($this,'ppr_summary_page') );
		add_action( 'admin_init', array($this,'register_pprsettings') );
	}

	function register_pprsettings() {
		register_setting( 'ppr-settings-group', 'ppr_use-custom-post-types' );
		register_setting( 'ppr-settings-group', 'ppr_override-nofollow' );
		register_setting( 'ppr-settings-group', 'ppr_override-newwindow' );
		register_setting( 'ppr-settings-group', 'ppr_override-redirect-type' );
		register_setting( 'ppr-settings-group', 'ppr_override-active' );
		register_setting( 'ppr-settings-group', 'ppr_override-URL' );
		register_setting( 'ppr-settings-group', 'ppr_override-rewrite' );
		register_setting( 'ppr-settings-group', 'ppr_meta-seconds' );
		register_setting( 'ppr-settings-group', 'ppr_meta-message' );
	}

	function ppr_summary_page() {?>
		<div class="wrap">
		<style type="text/css">.pprdonate{padding:5px;border:1px solid #dadada;font-family:tahoma, arial, helvetica, sans-serif;font-size:12px;float:right;position:absolute;top:25px;right:5px;width:250px;text-align:center;}.qform-table td{padding:2px !important;border:1px solid #cccccc;}.qform-table .headrow td{font-weight:bold;}.qform-table .onrow td{background-color:#eaeaea;}.qform-table .offrow td{background-color:#ffffff;}</style>
		<div class="icon32" style="<?php echo 'background: url('.WP_PLUGIN_URL.'/quick-pagepost-redirect-plugin/settings-icon.png) no-repeat transparent;';?>"><br></div>
		<h2>Quick Page Post Redirect Summary</h2>
		<p>This is a summary of Individual redirects only. Quick 301 Redirects can be found <a href="admin.php?page=redirect-updates">here</a>.</p><br/>
		<?php if($this->updatemsg!=''){?><div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo $this->updatemsg;?></strong></p></div><?php } ?>
		<?php $this->updatemsg ='';?>
		 <h2 style="font-size:20px;">Summary</h2>
		    <div align="left">
		    <table class="form-table qform-table" width="100%">
		        <tr valign="top" class="headrow">
		        	<td width="50" align="left">ID</td>
		        	<td width="75" align="left">post type</td>
		        	<td width="65" align="center">active</td>
		        	<td width="65" align="center">no follow</td>
		        	<td width="65" align="center">new win</td>
		        	<td width="60" align="center">type</td>
		        	<td width="50" align="center">rewrite</td>
		        	<td align="left">original URL</td>
		        	<td align="left">redirect to URL</td>

		        </tr>
		<?php 
			$tempReportArray = array();
			$tempa = array();
			$tempQTReportArray = array();
			if(count($this->quickppr_redirects)>0){
				foreach($this->quickppr_redirects as $key=>$redir){
					$tempQTReportArray = array('url'=>$key,'destinaition'=>$redir);
				}
			}
			if(count($this->ppr_all_redir_array)>0){
				foreach($this->ppr_all_redir_array as $key=>$result){
					$tempa['id']= $key;
					$tempa['post_type'] = get_post_type( $key );
					if(count($result)>0){
						foreach($result as $metakey => $metaval){
							$tempa[$metakey] = $metaval;
						}
					}
					$tempReportArray[] = $tempa;
					unset($tempa);
				}
			}
			if(count($tempReportArray)>0){
				$pclass = 'onrow';
				foreach($tempReportArray as $reportItem){
					$tactive = $reportItem['_pprredirect_active'];
					$trewrit = $reportItem['_pprredirect_rewritelink'];
					$tnofoll = $reportItem['_pprredirect_relnofollow'];
					$tnewwin = $reportItem['_pprredirect_newwindow'];
					$tretype = $reportItem['_pprredirect_type'];
					$tredURL = $reportItem['_pprredirect_url'];
					$tpotype = $reportItem['post_type'];
					$tpostid = $reportItem['id'];
					$toriurl = get_permalink($tpostid);
					if($pclass == 'offrow'){$pclass = 'onrow';}else{$pclass = 'offrow';}
					if($tnewwin == '0'){$tnewwin = '0';}else{$tnewwin = '1';}
					if($tredURL == 'http://www.example.com'){$tredURL='<strong>N/A - redirection will not occur</strong>';}
				?>     
		        <tr valign="top" class="<?php echo $pclass;?>">
		        	<td width="50" align="left"><?php echo $tpostid;?></td>
		        	<td width="75" align="left"><?php echo $tpotype;?></td>
		        	<td width="65" align="center"><?php echo $tactive;?></td>
		        	<td width="65" align="center"><?php echo $tnofoll;?></td>
		        	<td width="65" align="center"><?php echo $tnewwin;?></td>
		        	<td width="60" align="center"><?php echo $tretype;?></td>
		        	<td width="50" align="center"><?php echo $trewrit;?></td>
		        	<td align="left"><?php echo $toriurl;?></td>
		        	<td align="left"><?php echo $tredURL;?></td>
		        </tr>
				<?php }
			}
		 ?>
		    </table>
		</div>
		</div>
	<?php 
	} 

	function ppr_settings_page() {
	?>
	<div class="wrap" style="position:relative;">
	<style type="text/css">.pprdonate{padding:5px;border:1px solid #dadada;font-family:tahoma, arial, helvetica, sans-serif;font-size:12px;float:right;position:absolute;top:25px;right:5px;width:250px;text-align:center;}.qpprform label {float:left;display:block;width:290px;margin-left:30px;}.qpprform .submit{clear:both;}.qpprform span{font-size:9px;}</style>
	<div class="icon32" style="<?php echo 'background: url('.WP_PLUGIN_URL.'/quick-pagepost-redirect-plugin/settings-icon.png) no-repeat transparent;';?>"><br></div>
	<div class="pprdonate">
	<p>If you enjoy or find any of our plugins useful, please donate a few dollars to help with future development and updates.</p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input name="cmd" value="_s-xclick" type="hidden">
	<input name="hosted_button_id" value="8274582" type="hidden">
	<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" type="image"> <img src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt="" border="0" height="1" width="1"><br>
	</form>
	<p>We thank you in advance.</p>
	</div>
	<h2>Quick Page Post Redirect Options & Settings</h2>
	<?php if($this->updatemsg!=''){?><div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo $this->updatemsg;?></strong></p></div><?php } ?>
	<?php $this->updatemsg ='';//reset message;?>

	<form method="post" action="options.php" class="qpprform">
	    <?php settings_fields( 'ppr-settings-group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        	<td><h2 style="font-size:20px;">Basic Settings</h2></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Use with Custom Post Types?</label> <input type="checkbox" name="ppr_use-custom-post-types" value="1"<?php if(get_option('ppr_use-custom-post-types')=='1'){echo ' checked="checked" ';} ?>/></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Meta Refresh Time (in seconds):</label> <input type="text" size="5" name="ppr_meta-seconds" value="<?php echo get_option('ppr_meta-seconds');?>" /> <span>only needed for meta refresh. 0=default (instant)</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Meta Refresh Message:</label> <input type="text" size="25" name="ppr_meta-message" value="<?php echo get_option('ppr_meta-message');?>" /> <span>default is none. message to display while waiting for refresh</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><h2 style="font-size:20px;">Master Override Options</h2><b>NOTE: </b>These will override all individual settings.</td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Turn OFF all Redirects? </label><input type="checkbox" name="ppr_override-active" value="1"<?php if(get_option('ppr_override-active')=='1'){echo ' checked="checked" ';} ?>/> <span>excludes Quick 301 Redirects.</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Make ALL Redirects have <code>rel="nofollow"</code>? </label><input type="checkbox" name="ppr_override-nofollow" value="1"<?php if(get_option('ppr_override-nofollow')=='1'){echo ' checked="checked" ';} ?>/> <span>excludes Quick 301 Redirects.</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Make ALL Redirects open in a New Window? </label><input type="checkbox" name="ppr_override-newwindow" value="1"<?php if(get_option('ppr_override-newwindow')=='1'){echo ' checked="checked" ';} ?>/> <span>excludes Quick 301 Redirects.</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Make ALL Redirects this type: </label>
	        	<select name="ppr_override-redirect-type">
	        		<option value="0">Use Individual Settings</option>
	        		<option value="301" <?php if( get_option('ppr_override-redirect-type')=='301') {echo ' selected="selected" ';} ?>/>301 Permanant Redirect</option>
	        		<option value="302" <?php if( get_option('ppr_override-redirect-type')=='302') {echo ' selected="selected" ';} ?>/>302 Temporary Redirect</option>
	        		<option value="307" <?php if( get_option('ppr_override-redirect-type')=='307') {echo ' selected="selected" ';} ?>/>307 Temporary Redirect</option>
	        		<option value="meta" <?php if(get_option('ppr_override-redirect-type')=='meta'){echo ' selected="selected" ';} ?>/>Meta Refresh Redirect</option>
	        	</select>
	        	<span> (Quick 301 Redirects will always be 301)</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Make ALL Redirects go to this URL: </label><input type="text" size="50" name="ppr_override-URL" value="<?php echo get_option('ppr_override-URL'); ?>"/> <span>(use full URL including <code>http://</code>).</span></td>
	        </tr>
	        <tr valign="top">
	        	<td><label>Rewrite ALL Redirects URLs to Show in LINK? </label><input type="checkbox" name="ppr_override-rewrite" value="1"<?php if(get_option('ppr_override-rewrite')=='1'){echo ' checked="checked" ';} ?>/> <span>(makes link show redirect URL instead of the original URL).</span></td>
	        </tr>
	    </table>
	    <p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>
	</form>
	</div>
	<?php } 
	
	function ppr_options_page(){
	//generate the options page in the wordpress admin
		$tohash = $this->homelink.'/';
		?>
		<div class="wrap">
		<div class="icon32" style="<?php echo 'background: url('.WP_PLUGIN_URL.'/quick-pagepost-redirect-plugin/settings-icon.png) no-repeat transparent;';?>"><br></div>
		<script type="text/javascript">jQuery(document).ready(function() {jQuery(".delete-qppr").click(function(){var mainurl = '<?php echo get_bloginfo('url');?>/';	var thepprdel = jQuery(this).attr('id');if(confirm('Are you sure you want to delete this redirect?')){jQuery.ajax({url: mainurl+"/",data : "pprd="+thepprdel+"&scid=<?php echo md5($tohash);?>",success: function(data){jQuery('#row'+thepprdel).remove();},complete: function(){}});return false;}else{return false;}});});</script>
		<h2>Quick 301 Redirects</h2>
		<?php if($this->updatemsg!=''){?><div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php echo $this->updatemsg;?></strong></p></div><?php } ?>
		<?php $this->updatemsg ='';//reset message;?>
		<br/>This section is useful if you have links from an old site and need to have them redirect to a new location on the current site, or if you have an existing URL that you need to send some place else and you don't want to have a Page or Post created to use the other Page/Post Redirect option.
		<br/>
		<br/>To add additional 301 redirects, put the URL you want to redirect in the Request field and the Place you want it to redirect to, in the Destination field.
		<br/>To delete a redirect, click the 'x' next to the Destination Field.
		<br/>
		<br/><b style="color:red;">PLEASE NOTE:</b> 
		<ul>
		<li style="color:#214070;margin-left:25px;list-style-type:disc;">The <b>Request</b> field MUST be relative to the ROOT directory and contain the <code>/</code> at the beginning.</li> 
		<li style="color:#214070;margin-left:25px;list-style-type:disc;">The <b>Destination</b> field can be any valid URL or relative path (from root).</li>
		<li style="color:#214070;margin-left:25px;list-style-type:disc;">Quick Redirects <b>WILL NOT open in a new window</b>. They are meant to be 'Quick' and since the user will not 
		actually be clicking anything, a new window cannot be opened.</li>
		</ul>
		<br/>
		<form method="post" action="admin.php?page=redirect-updates">
		<table>
			<tr>
				<th align="left">Request</th>
				<th align="left">Destination</th>
				<th align="left">&nbsp;</th>
			</tr>
			<tr>
				<td><small>example: <code>/about.htm</code> or <code>/test-directory/landing-zone/</code></small></td>
				<td><small>example: <code><?php echo $this->homelink; ?>/about/</code> or  <code><?php echo $this->homelink; ?>/landing/</code></small></td>
				<td>&nbsp;</td>
			</tr>
			<?php echo $this->expand_redirects(); ?>
			<tr>
				<td><input type="text" name="quickppr_redirects[request][]" value="" style="width:35em" />&nbsp;&raquo;&nbsp;</td>
				<td><input type="text" name="quickppr_redirects[destination][]" value="" style="width:35em;" /></td>
				<td></td>
			</tr>
		</table>
		
		<p class="submit">
		<input type="submit" name="submit_301" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		</form>
		</div>
		
		<?php
	} 

	function save_redirects($data){
	// Save the redirects from the options page to the database
		$redirects = array();
		for($i = 0; $i < sizeof($data['request']); ++$i) {
			$request = trim($data['request'][$i]);
			if(strpos($request,'/',0) !== 0 && strpos($request,'http',0) !== 0){$request = '/'.$request;} // adds root marker to front if not there
			if((strpos($request,'.') === false && strpos($request,'?') === false) && strpos($request,'/',strlen($request)-1) === false){$request = $request.'/';} // adds end folder marker if not a file end
			$destination = trim($data['destination'][$i]);
			if (($request == '' || $request == '/') && $destination == '') { continue; } elseif($request != '' && $request != '/' && $destination == '' ){ $redirects[$request] = $this->homelink.'/';}else { $redirects[$request] = $destination; }
		}
		update_option('quickppr_redirects', $redirects);
		return $redirects;
	}
	
	function expand_redirects(){
	//utility function to return the current list of redirects as form fields
		$output = '';
		if (!empty($this->quickppr_redirects)) {
			$ww=1;
			foreach ($this->quickppr_redirects as $request => $destination) {
				$output .= '
				<tr id="rowpprdel-'.$ww.'">
					<td><input type="text" name="quickppr_redirects[request][]" value="'.$request.'" style="width:35em" />&nbsp;&raquo;&nbsp;</td>
					<td><input type="text" name="quickppr_redirects[destination][]" value="'.$destination.'" style="width:35em;" /></td>
					<td>&nbsp;<a href="javascript:void();" id="pprdel-'.$ww.'" class="delete-qppr">x</a>&nbsp;</td>
				</tr>
				';
				$ww++;
			}
		}
		return $output;
	}
	

	function ppr_filter_links ($link = '', $post = array()) {
		if(isset($post->ID)){	
			$id = $post->ID;
		}else{
			$id = $post;
		}
		if(array_key_exists($id, $this->ppr_all_redir_array)){
			$matchedID = $this->ppr_all_redir_array[$id];
			if($matchedID['_pprredirect_rewritelink'] == '1' || $this->pproverride_rewrite =='1'){ // if rewrite link is checked or override is set
				if($this->pproverride_URL ==''){$newURL = $matchedID['_pprredirect_url'];}else{$newURL = $this->pproverride_URL;} // check override
				if(strpos($newURL,$this->homelink)>=0 || strpos($newURL,'www.')>=0 || strpos($newURL,'http://')>=0 || strpos($newURL,'https://')>=0){
					$link = esc_url( $newURL );
				}else{
					$link = esc_url( $this->homelink.'/'. $newURL );
				}
			}
		}

		return $link;
	}
	function ppr_filter_page_links ($link, $post) {
		if(isset($post->ID)){	
			$id = $post->ID;
		}else{
			$id = $post;
		}
		if(array_key_exists($id, $this->ppr_all_redir_array)){
			$matchedID = $this->ppr_all_redir_array[$id];
			if($matchedID['_pprredirect_rewritelink'] == '1' || $this->pproverride_rewrite =='1'){ // if rewrite link is checked
				if($this->pproverride_URL ==''){$newURL = $matchedID['_pprredirect_url'];}else{$newURL = $this->pproverride_URL;} // check override
				if(strpos($newURL,$this->homelink)>=0 || strpos($newURL,'www.')>=0 || strpos($newURL,'http://')>=0 || strpos($newURL,'https://')>=0){
					$link = esc_url( $newURL );
				}else{
					$link = esc_url( $this->homelink.'/'. $newURL );
				}
			}
		}

		return $link;
	}
	
	
	function ppr_add_metabox(){
		if( function_exists( 'add_meta_box' ) ) {
			$usetypes = get_option('ppr_use-custom-post-types')!= '' ? get_option('ppr_use-custom-post-types') : '0';
			if($usetypes == '1'){
				$post_types_temp = get_post_types();
				if(count($post_types_temp)==0){
					$post_types_temp = array('page' => 'page','post' => 'post','attachment' => 'attachment','nav_menu_item' => 'nav_menu_item');
				}
			}else{
				$post_types_temp = array('page' => 'page','post' => 'post','attachment' => 'attachment','nav_menu_item' => 'nav_menu_item');
			}
			unset($post_types_temp['revision']); //remove revions from array if present as they are not needed.
			foreach($post_types_temp as $type){
				add_meta_box( 'edit-box-ppr', 'Quick Page/Post Redirect', array($this,'edit_box_ppr_1'), $type, 'normal', 'high' ); 
			}
		}
	}
	
	function get_main_array(){
		global $wpdb;
		$theArray = array();
		$thetemp = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE (`meta_key` = '_pprredirect_active' OR `meta_key` = '_pprredirect_rewritelink' OR `meta_key` = '_pprredirect_newwindow' OR `meta_key` = '_pprredirect_relnofollow' OR `meta_key` = '_pprredirect_type' OR `meta_key` = '_pprredirect_url') ORDER BY `post_id` ASC;");
		if(count($thetemp)>0){
			foreach($thetemp as $key){
				$theArray[$key->post_id][$key->meta_key] = $key->meta_value;
			}
			foreach($thetemp as $key){
				// defaults
				if(!isset($theArray[$key->post_id]['_pprredirect_rewritelink'])){$theArray[$key->post_id]['_pprredirect_rewritelink']	= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_url'])){$theArray[$key->post_id]['_pprredirect_url']					= 'http://www.example.com';}
				if(!isset($theArray[$key->post_id]['_pprredirect_type'] )){$theArray[$key->post_id]['_pprredirect_type']				= 302;}
				if(!isset($theArray[$key->post_id]['_pprredirect_relnofollow'])){$theArray[$key->post_id]['_pprredirect_relnofollow']	= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_newwindow'] ))	{$theArray[$key->post_id]['_pprredirect_newwindow']	= 0;}
				if(!isset($theArray[$key->post_id]['_pprredirect_active'] )){$theArray[$key->post_id]['_pprredirect_active']			= 0;}
			}

		}
		return $theArray;
	}
	
	function get_value($theval='none'){
		if($theval==''){return;}
		switch($theval){
			case 'ppr_all_redir_array':
				return $this->ppr_all_redir_array;
				break;
			case 'ppr_nofollow':
				return $this->ppr_all_redir_array;
				break;
			case 'ppr_newindow':
				return $this->ppr_newindow;
				break;
			case 'ppr_url':
				return $this->ppr_url;
				break;
			case 'ppr_url_rewrite':
				return $this->ppr_url_rewrite;
				break;
			case 'ppr_type':
				return $this->ppr_type;
				break;
			case 'ppr_curr_version':
				return $this->ppr_curr_version;
				break;
			case 'ppr_metaurlnew':
				return $this->ppr_metaurlnew;
				break;
			case 'hepprversion':
				return $this->hepprversion;
				break;
			case 'thepprmeta':
				return $this->thepprmeta;
				break;
			case 'quickppr_redirects':
				return $this->quickppr_redirects;
				break;
			case 'tohash':
				return $this->tohash;
				break;
			case 'fcmlink':
				return $this->fcmlink;
				break;
			case 'adminlink':
				return $this->adminlink;
				break;
			case 'homelink':
				return $this->homelink;
				break;
			case 'updatemsg':
				return $this->updatemsg;
				break;
			case 'pprmeta_seconds':
				return $this->pprmeta_seconds;
				break;
			case 'pprmeta_message':
				return $this->pprmeta_message;
				break;
			case 'none':
				return 0;
				break;
		}

		return '';
	}
	
	function ppr_addmetatohead_theme(){
		// check URL override
	    if($this->pproverride_URL !=''){$urlsite = $this->pproverride_URL;} else {$urlsite = $this->ppr_metaurl;}
	    $this->pproverride_URL = ''; //reset
	    if($this->pprmeta_seconds==''){$this->pprmeta_seconds='0';}
		echo '<meta http-equiv="refresh" content="'.$this->pprmeta_seconds.'; URL='.$urlsite.'" />';
		if($this->pprmeta_message!='' && $this->pprmeta_seconds!='0'){echo '<div style="margin-top:20px;text-align:center;">'.$this->pprmeta_message.'</div>';}
		exit; //stop loading page so meta can do it's job without rest of page loading.
	}

	function ppr_queryhook($vars) {
		$vars[] = 'pprd';
		$vars[] = 'scid';
		return $vars;
	}

	function ppr_parse_request($wp) {
		global $wp;
		if(array_key_exists('pprd', $wp->query_vars) && array_key_exists('scid', $wp->query_vars)){
			$tohash = get_bloginfo('url').'/';
			if( $wp->query_vars['pprd'] !='' && $wp->query_vars['scid'] == md5($tohash)){
				$theDel = str_replace('pprdel-','',$wp->query_vars['pprd']);
				$redirects = get_option('quickppr_redirects');
				if (!empty($redirects)) {
					$ww=1;
					foreach ($redirects as $request => $destination) {
						if($ww != (int)$theDel){
							$quickppr_redirects[$request] = $destination;
						}
					$ww++;
					}
				} // end if
				update_option('quickppr_redirects',$quickppr_redirects);
				echo 1;
				exit;
			}else{
				echo 0;
				exit;
			}
		}else{
			return;
		}
	}
	
	function ppr_init_check_version() {
	// checks version of plugin in DB and updates if needed.
		global $wpdb;
		if($this->thepprversion != $this->ppr_curr_version){
			update_option( 'ppr_version', $this->ppr_curr_version );
		}
		if($this->thepprmeta != '1'){
			update_option( 'ppr_meta_clean', '1' );
			$wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = CONCAT('_',`meta_key`) WHERE `meta_key` = 'pprredirect_active' OR `meta_key` = 'pprredirect_rewritelink' OR `meta_key` = 'pprredirect_newwindow' OR `meta_key` = 'pprredirect_relnofollow' OR `meta_key` = 'pprredirect_type' OR `meta_key` = 'pprredirect_url';");
		}
	}

	function ppr_filter_plugin_actions($links){
		$new_links = array();
		$new_links[] = '<a href="'.$this->fcmlink.'/donate/">Donate</a>';
		return array_merge($links,$new_links );
	}
	
	function ppr_filter_plugin_links($links, $file){
		if ( $file == plugin_basename(__FILE__) ){
			$links[] = '<a href="'.$this->adminlink.'admin.php?page=redirect-updates">Quick Redirects</a>';
			$links[] = '<a target="_blank" href="'.$this->fcmlink.'/quick-pagepost-redirect-plugin/">FAQ</a>';
			$links[] = '<a target="_blank" href="'.$this->fcmlink.'/donate/">Donate</a>';
		}
		return $links;
	}
	
	function edit_box_ppr_1() {
	// Prints the inner fields for the custom post/page section 
		global $post;
		$ppr_option1='';
		$ppr_option2='';
		$ppr_option3='';
		$ppr_option4='';
		$ppr_option5='';
		// Use nonce for verification ... ONLY USE ONCE!
		wp_nonce_field( 'pprredirect_noncename', 'pprredirect_noncename', false, true );

		// The actual fields for data entry
		echo '<label for="pprredirect_active" style="padding:2px 0;"><input type="checkbox" name="pprredirect_active" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_active',true),0).' />Make Redirect <b>Active</b>. (check to turn on)</label><br />';
		echo '<label for="pprredirect_newwindow" style="padding:2px 0;"><input type="checkbox" name="pprredirect_newwindow" id="pprredirect_newwindow" value="_blank" '. checked('_blank',get_post_meta($post->ID,'_pprredirect_newwindow',true),0).'>Open redirect link in a <b>new window.</b></label><br />';
		echo '<label for="pprredirect_relnofollow" style="padding:2px 0;"><input type="checkbox" name="pprredirect_relnofollow" id="pprredirect_relnofollow" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_relnofollow',true),0).'>Add <b>rel=\"nofollow\"</b> to redirect link.</label><br />';
		echo '<label for="pprredirect_rewritelink" style="padding:2px 0;"><input type="checkbox" name="pprredirect_rewritelink" id="pprredirect_rewritelink" value="1" '. checked('1',get_post_meta($post->ID,'_pprredirect_rewritelink',true),0).'><b>Show</b> the Redirect URL below in the link instead of this page URL. <b>NOTE: You may have to use the <u>FULL</u> URL below!</b></label><br /><br />';
		echo '<label for="pprredirect_url"><b>Redirect URL:</b></label><br />';
		if(get_post_meta($post->ID, '_pprredirect_url', true)!=''){$pprredirecturl=get_post_meta($post->ID, '_pprredirect_url', true);}else{$pprredirecturl="";}
		echo '<input type="text" style="width:75%;margin-top:2px;margin-bottom:2px;" name="pprredirect_url" value="'.$pprredirecturl.'" /><br />(i.e., <code>http://example.com</code> or <code>/somepage/</code> or <code>p=15</code> or <code>155</code>. Use <b>FULL URL</b> <i>including</i> <code>http://</code> for all external <i>and</i> meta redirects. )<br /><br />';
	
		echo '<label for="pprredirect_type">Type of Redirect:</label> ';
		if(get_post_meta($post->ID, '_pprredirect_type', true)!=''){$pprredirecttype=get_post_meta($post->ID, '_pprredirect_type', true);}else{$pprredirecttype="";}
		switch($pprredirecttype):
			case "":
				$ppr_option2=" selected";//default
				break;
			case "301":
				$ppr_option1=" selected";
				break;
			case "302":
				$ppr_option2=" selected";
				break;
			case "307":
				$ppr_option3=" selected";
				break;
			case "meta":
				$ppr_option5=" selected";
				break;
		endswitch;
		
		echo '<select style="margin-top:2px;margin-bottom:2px;width:40%;" name="pprredirect_type"><option value="301" '.$ppr_option1.'>301 Permanent</option><option value="302" '.$ppr_option2.'>302 Temporary</option><option value="307" '.$ppr_option3.'>307 Temporary</option><option value="meta" '.$ppr_option5.'>Meta Redirect</option></select> (Default is 302)<br /><br />';
		echo '<b>NOTE:</b> For This Option to work, the page or post needs to be published for the redirect to happen <i><b>UNLESS</b></i> you publish it first, then save it as a Draft. If you want to add a redirect without adding a page/post or having it published, use the <a href="./admin.php?page=redirect-updates">Quick Redirects</a> method.';
	}
	
	function ppr_save_metadata($post_id, $post) {
		if($post->post_type == 'revision'){return;}
		if(isset($_REQUEST['pprredirect_noncename']) && (isset($_POST['pprredirect_active']) || isset($_POST['pprredirect_url']) || isset($_POST['pprredirect_type']) || isset($_POST['pprredirect_newwindow']) || isset($_POST['pprredirect_relnofollow']))):
			unset($my_meta_data);
			$my_meta_data = array();
			// verify authorization
			if(isset($_POST['pprredirect_noncename'])){
				if ( !wp_verify_nonce( $_REQUEST['pprredirect_noncename'], 'pprredirect_noncename' )) {
					return $post_id;
				}
			}
			// check allowed to editing
			if ( !(current_user_can('edit_page', $post_id ) || current_user_can( 'edit_post', $post_id ))){
					return $post_id;
			}
			
			// find & save the form data & put it into an array
			$my_meta_data['_pprredirect_active'] 		= $_REQUEST['pprredirect_active'];
			$my_meta_data['_pprredirect_newwindow'] 	= $_REQUEST['pprredirect_newwindow'];
			$my_meta_data['_pprredirect_relnofollow'] 	= $_REQUEST['pprredirect_relnofollow'];
			$my_meta_data['_pprredirect_type'] 			= $_REQUEST['pprredirect_type'];
			$my_meta_data['_pprredirect_rewritelink'] 	= $_REQUEST['pprredirect_rewritelink'];
			$my_meta_data['_pprredirect_url']    		= $_REQUEST['pprredirect_url']; //stripslashes( $_POST['pprredirect_url']);
			
			if ( 0 === strpos($my_meta_data['_pprredirect_url'], 'www.'))
				$my_meta_data['_pprredirect_url'] = 'http://' . $my_meta_data['_pprredirect_url'] ; // Starts with www., so add http://

			if($my_meta_data['_pprredirect_url'] == ''){
				$my_meta_data['_pprredirect_type'] 		= NULL; //clear Type if no URL is set.
				$my_meta_data['_pprredirect_active'] 	= NULL; //turn it off if no URL is set
			}
			
			// Add values of $my_meta_data as custom fields
			if(count($my_meta_data)>0){
				foreach ($my_meta_data as $key => $value) { //Let's cycle through the $my_meta_data array!
					if( $post->post_type == 'revision' ){ return; } //don't store custom data twice
					$value = implode(',', (array)$value); //if $value is an array, make it a CSV (unlikely)
					
					if($value=='' || $value == NULL){ 
						delete_post_meta($post->ID, $key); 
					}else{
						if(get_post_meta($post->ID, $key, true) != '') {
							update_post_meta($post->ID, $key, $value);
						} else { 
							add_post_meta($post->ID, $key, $value);
						}
					}
				}
			}
		endif;
	}
	
	function ppr_fix_targetsandrels($pages) {
		$ppr_url 		= array();
		$ppr_newindow 	= array();
		$ppr_nofollow 	= array();
		
		if (empty($ppr_url) && empty($ppr_newindow) && empty($ppr_nofollow)){
			$thefirstppr = array();
			if(!empty($this->ppr_all_redir_array)){
				foreach($this->ppr_all_redir_array as $key => $pprd){
					foreach($pprd as $ppkey => $pprs){
						$thefirstppr[$key][$ppkey] = $pprs;
						$thefirstppr[$key]['post_id'] = $key;
					}
				}
			}
			if(!empty($thefirstppr)){
				foreach($thefirstppr as $ppitems){
					if($ppitems['_pprredirect_active'] == 1 && $this->pproverride_newwin =='1'){ // check override of NEW WINDOW
							$ppr_newindow[] = $ppitems['post_id'];
					}else{
						if($ppitems['_pprredirect_active'] == 1 && $ppitems['_pprredirect_newwindow'] === '_blank'){
							$ppr_newindow[] = $ppitems['post_id'];
						}
					}
					
					if($ppitems['_pprredirect_active']==1 && $this->pproverride_nofollow =='1'){ //check override of NO FOLLOW
						$ppr_nofollow[] = $ppitems['post_id'];
					}else{
						if($ppitems['_pprredirect_active']==1 && $ppitems['_pprredirect_relnofollow'] == 1){
							$ppr_nofollow[] = $ppitems['post_id'];
						}
					}
					
					if($ppitems['_pprredirect_active']==1 && $this->pproverride_rewrite =='1'){ //check override of REWRITE
						if($this->pproverride_URL!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $this->pproverride_URL; //check override of URL
						}elseif($ppitems['_pprredirect_url']!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $ppitems['_pprredirect_url'];
						}
					}else{
						if($ppitems['_pprredirect_active']==1 && $ppitems['_pprredirect_rewritelink'] == '1' && $ppitems['_pprredirect_url']!=''){
							$ppr_url_rewrite[] = $ppitems['post_id'];
							$ppr_url[$ppitems['post_id']]['URL'] = $ppitems['_pprredirect_url'];
						}
					}
				}
			}

			if (count($ppr_newindow)<0 && count($ppr_nofollow)<0){
				return $pages;
			}
		}
		
		$this_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if(count($ppr_nofollow)>=1) {
			foreach($ppr_nofollow as $relid){
			$validexp="@\<li(?:.*?)".$relid."(?:.*?)\>\<a(?:.*?)rel\=\"nofollow\"(?:.*?)\>@i";
			$found = preg_match_all($validexp, $pages, $matches);
				if($found!=0){
					$pages = $pages; //do nothing 'cause it is already a rel=nofollow.
				}else{
					$pages = preg_replace('@<li(.*?)-'.$relid.'(.*?)\>\<a(.*?)\>@i', '<li\1-'.$relid.'\2><a\3 rel="nofollow">', $pages);
				}
			}
		}
		
		if(count($ppr_newindow)>=1) {
			foreach($ppr_newindow as $p){
				$validexp="@\<li(?:.*?)".$p."(?:.*?)\>\<a(?:.*?)target\=(?:.*?)\>@i";
				$found = preg_match_all($validexp, $pages, $matches);
				if($found!=0){
					$pages = $pages; //do nothing 'cause it is already a target=_blank.
				}else{
					$pages = preg_replace('@<li(.*?)-'.$p.'(.*?)\>\<a(.*?)\>@i', '<li\1-'.$p.'\2><a\3 target="_blank">', $pages);
				}
			}
		}
		return $pages;
	}
	
	function redirect_post_type(){
		return;
		//not needed at this time
	}
	
	function getAddress(){
	// utility function to get the full address of the current request - credit: http://www.phpro.org/examples/Get-Full-URL.html
		if(!isset($_SERVER['HTTPS'])){$_SERVER['HTTPS']='';}
		$protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http'; //check for https
		return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; //return the full address
	}
	
	function redirect(){
	// Read the list of redirects and if the current page is found in the list, send the visitor on her way
		if (!empty($this->quickppr_redirects) && !is_admin()) {
			$userrequest = str_replace(get_option('home'),'',$this->getAddress());
			if(strpos($userrequest,'/') === false) {$userrequesttest = '/'.$userrequest.'/';}else{$userrequesttest = $userrequest.'/';}
			if (array_key_exists($userrequest,$this->quickppr_redirects) || array_key_exists($userrequesttest,$this->quickppr_redirects)) {
				if(isset($this->quickppr_redirects[$userrequesttest]) && $this->quickppr_redirects[$userrequesttest]!=''){$userrequest = $userrequesttest;}
				if($this->quickppr_redirects[$userrequest]!='') {
					if($this->pproverride_URL!=''){$useURL = $this->pproverride_URL;}else{$useURL = $this->quickppr_redirects[$userrequest];}
					switch($this->pproverride_type){
						case '':
						case '0':
						case '301':
							header ('Status: 301');
							header ('HTTP/1.1 301 Moved Permanently');
							break;
						case '302':
							header ('Status: 302');
							header ('HTTP/1.1 302 Moved Temporarily');
							break;
						case '307':
							header ('Status: 307');
							header ('HTTP/1.1 307 Moved Temporarily');
							break;
						case 'meta':
							$this->ppr_metaurl = $useURL;
							add_action('wp_head', array($this,'ppr_addmetatohead_theme'),1);
							break;
					}
					//check URL Override
					header ('Location: ' . $useURL);
					exit();
				}
			}
		}
	}

	function ppr_do_redirect(){
	// Read the list of redirects and if the current page is found in the list, send the visitor on her way
		global $post;
		if (count($this->ppr_all_redir_array)>0 && (is_single() || is_singular() || is_page())) {
			if(isset($this->ppr_all_redir_array[$post->ID])){
				$isactive = $this->ppr_all_redir_array[$post->ID]['_pprredirect_active'];
				$redrtype = $this->ppr_all_redir_array[$post->ID]['_pprredirect_type'];
				$redrurl  = $this->ppr_all_redir_array[$post->ID]['_pprredirect_url'];
				if($isactive == 1 && $redrurl != '' && $redrurl != 'http://www.example.com'){
					if($redrtype === 0){$redrtype = '200';}
					if($redrtype === ''){$redrtype = '302';}
					
					if( strpos($redrurl, 'http://')=== 0 || strpos($redrurl, 'https://')=== 0){
						$urlsite=$redrurl;
					}elseif(strpos($redrurl, 'www')=== 0){ //check if they have full url but did not put http://
						$urlsite='http://'.$redrurl;
					}elseif(is_numeric($redrurl)){ // page/post number
						$urlsite=$this->homelink.'/?p='.$redrurl;
					}elseif(strpos($redrurl,'/') === 0){ // relative to root	
						$urlsite = $this->homelink.$redrurl;
					}else{	// we assume they are using the permalink / page name??
						$urlsite=$this->homelink.'/'.$redrurl;
					}
					
					// check if override is set for all redirects to go to one URL
					if($this->pproverride_URL !=''){$urlsite=$this->pproverride_URL;} 
					if($this->pproverride_type!='0' && $this->pproverride_type!=''){$redrtype = $this->pproverride_type;} //override check
					
					header("Status: $redrtype");
					switch($redrtype){
						case '301':
						case '0':
						case '':
							header ('HTTP/1.1 301 Moved Permanently');
							header("Location: $urlsite", true, $redrtype);
							exit(); //stop loading page
							break;
						case '302':
							header ('HTTP/1.1 302 Moved Temporarily');
							header("Location: $urlsite", true, $redrtype);
							exit(); //stop loading page
							break;
						case '307':
							header ('HTTP/1.1 307 Moved Temporarily');
							header("Location: $urlsite", true, $redrtype);
							exit(); //stop loading page
							break;
						case 'meta':
							$this->ppr_metaurl = $redrurl;
							add_action('wp_head', array($this,'ppr_addmetatohead_theme'),1);
							break;
					}
				}
			}
		}
	}
	
	function ppr_new_nav_menu_fix($ppr){
		$newmenu = array();
		if(!empty($ppr)){
			foreach($ppr as $ppd){
				if(isset($this->ppr_all_redir_array[$ppd->object_id])){
					$theIsActives 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_active'];
					$theNewWindow 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_newwindow'];
					$theNoFollow 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_relnofollow'];
					$theRewrite 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_rewritelink'];
					$theRedURL	 	= $this->ppr_all_redir_array[$ppd->object_id]['_pprredirect_url'];
					
					if($this->pproverride_URL !=''){$theRedURL = $this->pproverride_URL;} // check override

					if($theIsActives == '1' && $theNewWindow === '_blank'){
						$ppd->target = '_blank';
						$ppd->classes[] = 'ppr-new-window';
					}
					if($theIsActives == '1' && $theNoFollow == '1'){
						$ppd->xfn = 'nofollow';
						$ppd->classes[] = 'ppr-nofollow';
					}
					if($theIsActives == '1' && $theRewrite == '1' && $theRedURL != ''){
						$ppd->url = $theRedURL;
						$ppd->classes[] = 'ppr-rewrite';
	
					}
				}
				$newmenu[] = $ppd;
			}
		}
		return $newmenu;
	}

}
//=======================================
// END Main Redirect Class.
//=======================================

$redirect_plugin = new quick_page_post_reds(); // call our class
?>