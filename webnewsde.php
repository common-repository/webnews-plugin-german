<?php
/*
Plugin Name: Webnewsde
Plugin URI: http://www.webnews.de
Description: Allowes user to submit written stories directly to webnews service.
Author: Webnews Team
Version: 0.1
Author URI: http://www.webnews.de
Text Domain: webnewsde
*/

/*  Copyright 2010  Webnews.de  (email : webnews [A-T] webnews.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( !class_exists( 'Webnewsde' ) || ( defined( 'WP_DEBUG') && WP_DEBUG ) ) {
	class Webnewsde {
		
		var $news_categories = array(
			array(
				'catID'				=> '13',		
				'catName'			=> 'Politische News',
			),
			array(
				'catID'				=> '14',
				'catName'			=> 'Politische Meinung',
			),
			array(
				'catID'				=> '15',
				'catName'			=> 'Politische Europa/Welt',
			),
			array(
				'catID'				=> '49',
				'catName'			=> 'Wirtschaft Inland',
			),
			array(
				'catID'				=> '50',
				'catName'			=> 'Wirtschaft Auslan',
			),
			array(
				'catID'				=> '51',
				'catName'			=> 'Börse',
			),
			array(
				'catID'				=> '2',
				'catName'			=> 'Apple',
			),
			array(
				'catID'				=> '4',
				'catName'			=> 'Gadgets',
			),
			array(
				'catID'				=> '5',
				'catName'			=> 'Hardware',
			),
			array(
				'catID'				=> '6',
				'catName'			=> 'Branchennews Tech',
			),
			array(
				'catID'				=> '9',
				'catName'			=> 'Software',
			),
			array(
				'catID'				=> '52',
				'catName'			=> 'Automobil',
			),
			array(
				'catID'				=> '39',
				'catName'			=> 'Kino',
			),
			array(
				'catID'				=> '40',
				'catName'			=> 'Musik',
			),
			array(
				'catID'				=> '41',
				'catName'			=> 'Fernsehen',
			),
			array(
				'catID'				=> '42',
				'catName'			=> 'Kultur',
			),
			array(
				'catID'				=> '53',
				'catName'			=> 'Leute',
			),
			array(
				'catID'				=> '54',
				'catName'			=> 'Witziges & Skurriles',
			),
			array(
				'catID'				=> '69',
				'catName'			=> 'Panorama',
			),
			array(
				'catID'				=> '56',
				'catName'			=> 'Tipps & Tricks',
			),
			array(
				'catID'				=> '57',
				'catName'			=> 'Reise',
			),
			array(
				'catID'				=> '58',
				'catName'			=> 'Zuhause',
			),
			array(
				'catID'				=> '59',
				'catName'			=> 'Vergnügen',
			),
			array(
				'catID'				=> '60',
				'catName'			=> 'Gesundheit',
			),
			array(
				'catID'				=> '61',
				'catName'			=> 'Shopping',
			),
			array(
				'catID'				=> '62',
				'catName'			=> 'Mode',
			),
			array(
				'catID'				=> '25',
				'catName'			=> 'Basketball',
			),
			array(
				'catID'				=> '26',
				'catName'			=> 'Extremsport',
			),
			array(
				'catID'				=> '27',
				'catName'			=> 'Fussball',
			),
			array(
				'catID'				=> '28',
				'catName'			=> 'Motorsport',
			),
			array(
				'catID'				=> '29',
				'catName'			=> 'Tennis',
			),
			array(
				'catID'				=> '31',
				'catName'			=> 'Andere Sportarten',
			),
			array(
				'catID'				=> '63',
				'catName'			=> 'US-Sport',
			),
			array(
				'catID'				=> '33',
				'catName'			=> 'Forschung',
			),
			array(
				'catID'				=> '34',
				'catName'			=> 'Umwelt & Natur',
			),
			array(
				'catID'				=> '35',
				'catName'			=> 'Weltraum',
			),
			array(
				'catID'				=> '36',
				'catName'			=> 'Wissenschaft Sonstiges',
			),
			array(
				'catID'				=> '44',
				'catName'			=> 'Spiele News',
			),
			array(
				'catID'				=> '45',
				'catName'			=> 'Online Spiele',
			),
		);

		
		
		var $errors = array();
		var $post_page = false;
		// Link counter - used by RX for removing links
		var $link_counter = 0;
		
		// Constructor
		function Webnewsde() {
			
			// Save post - filter for data
			add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post' ), 100, 2 );
			// Save post - called after post is saved
			add_action( 'save_post', array( &$this, 'save_post' ) );
			// Display notices in Admin panel
			add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
			// Required to check if we are on Edit Post page
			add_action( 'do_meta_boxes', array( &$this, 'do_meta_boxes' ) );
			// Default post template handling
		//	add_action( 'submitpost_box', array( &$this, 'submitpost_box' ) );
			// Add option to Admin menu
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			// Initialize plugin
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );

		}
		
		// Plugin initialization
		function init() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'webnewsde', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)) );
			}
		}
		
		
		function admin_init(){
			register_setting( 'webnewsde', 'webnewsde_settings_username' );
			register_setting( 'webnewsde', 'webnewsde_settings_password', array( &$this, 'hash_password' ) );
			register_setting( 'webnewsde', 'webnewsde_settings_enable' );
		}
		
	
		// Add Admin menu option
		function admin_menu() {
			
			add_submenu_page( 'options-general.php', 'Webnews.de Settings', 
				'Webnews.de', 10, __FILE__, array( $this, 'options_panel' ) );
			
			// Add metabox to edit post page
			add_meta_box( 'webnewsde_post_section', 'Webnews.de', array( &$this, 'post_metabox' ), 'post', 'normal', 'high' );
		}
		
		// Check if we are on Edit Post page
		function do_meta_boxes($type) {
			if ( $type == 'post' ) {
				$this->post_page = true;
			}
		}
		
		// Display notice in Admin panel
		function admin_notice() {
			global $post;
			$meta = '';
			if ( $this->post_page && is_object( $post) ) {
				$meta = get_post_meta($post->ID, 'Webnewsde_msg', true);
			}
			if ('' != $meta ) {
				// Display error message
				echo '<div id="notice" class="error"><p>', $meta, 
					'<br />', __('Der Status deines Betrag wurde auf Entwurf gesetzt.', 'webnewsde'), '</p></div>', "\n";
				// Remove this message
				delete_post_meta( $post->ID, 'Webnewsde_msg' );
				// Change WP message to 'Post saved'
				if ( isset( $_GET['message'] ) ) {
					if ( '6' == $_GET['message'] ) {
						$_GET['message'] = '7';
					}
				}
			}
		}
		
		// Check submitted post data
		function wp_insert_post( $data, $post_arr ) {

			$ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
			$skip_check = false;
			if ( $ajax && !$autosave ) { // Quickedit
				$skip_check = true;
			}

			if( $post_arr['webnewsde_publish_category'] != '' ){
				add_post_meta( $post_arr['ID'] , 'webnewsde_publish_category', $post_arr['webnewsde_publish_category'] );				
			}
			

			
			if( !$skip_check ){
				if ( ( 'post' == $data['post_type']) && ( ( 'publish' == $data['post_status'] ) || ( 'pending' == $data['post_status'] ) ) ){
					if( isset($post_arr['webnewsde_publish_use']) && $post_arr['webnewsde_publish_use'] == 'on' ){
						
						if( empty( $post_arr['tags_input'] ) && empty( $post_arr['tax_input']['post_tag'] )  ){
							$this->errors[] = __('Webnews-Verlinkung fehlgeschlagen: Es ist mindestens ein Stichwort (engl. \'tag\') notwending.');
							$data['post_status'] = 'draft';
						}
						
						if( empty( $post_arr['post_title'] ) ){
							$this->errors[] = __('Webnews-Verlinkung fehlgeschlagen: Dein Beiträg benötigt eine Überschrift.');
							$data['post_status'] = 'draft';					
						}
						
						if( empty( $post_arr['content'] ) ){
							$this->errors[] = __('Webnews-Verlinkung fehlgeschlagen: Dein Beitrag darf nicht leer sein.');
							$data['post_status'] = 'draft';
						}
						
						if( get_option( 'webnewsde_settings_username' ) == '' || get_option( 'webnewsde_settings_password' ) == '' ){
							$this->errors[] = __('Gib hier deinen Webnews Nutzernamen und Passwort an.');
							$data['post_status'] = 'draft';	
						}
					}
				}
	
				if( $post_arr['webnewsde_publish_use'] == 'on' ){
					add_post_meta( $post_arr['ID'] , 'webnewsde_enable_submit', true );
					
					if( $data['post_status'] == 'publish' && $post_arr['visibility'] == 'public' ){
						
						if( empty($post_arr['tags_input']) && !empty($post_arr['tax_input']['post_tag']) ){
							$tags = $post_arr['tax_input']['post_tag'];
						}else{
							$tags = implode( ',', $post_arr['tags_input'] ) ;						
						}
						
						$send_data = array(
							'title' 			=> $data['post_title'],
							'content'			=> $post_arr['content'],
							'link'				=> $data['guid'],
							'catID'				=> $post_arr['webnewsde_publish_category'],
							'tags'				=> $tags,
							'username'			=> get_option( 'webnewsde_settings_username' ),
							'password'			=> get_option( 'webnewsde_settings_password' ),
							'useContentImage'	=> $post_arr['webnewsde_image_from_content'],
						);
						
						list( $header, $answer ) = $this->PostRequest( 'http://tools.webnews.de/wp', $send_data );
						
						$_tmp = explode( '|', $answer );
						$answer = $_tmp[0];
						
						if( !empty($_tmp[1] ) ){
							add_post_meta( $post_arr['ID'] , 'storyID', intval( $_tmp[1] ) );
						}
						
										
						switch( trim($answer) ){
							case 'storyAdded':
								add_post_meta(  $post_arr['ID'] , 'webnewsde_post_submited', true );
								break;
							case 'storyAddedAndBlacklisted':
								add_post_meta(  $post_arr['ID'] , 'webnewsde_post_submited', true );
								$this->errors[] = __('Deine Nachricht wurde verlinkt, aber noch nicht veröffentlicht.');
								break;
							case 'wrongNumberOfParameters': 
								$this->errors[] = __('Warnung: Nicht alle Informationen wurden an Webnews gesendet.');
								$data['post_status'] = 'draft';
								break;
							case 'userNotActive':
								$this->errors[] = __('Anmeldung bei Webnews fehlgeschlagen. Prüfe Nutzername und Passwort.');
								$data['post_status'] = 'draft';
								break;
							case 'urlNotActiveOrUsed':;
								$data['post_status'] = 'draft';
								$this->errors[] = __('Diese URL wurde bereits verlinkt und kann nicht erneut übertragen werden.');
								break;		
							case 'errorDuringStoryAdd':
								$this->errors[] = __('Es ist ein Verbindungsfehler zu Webnews aufgetreten. Versuche es später noch einmal.');
								$data['post_status'] = 'draft';
								break;
							default :
								$this->errors[] = 'Es ist ein unbekannter Fehler bei der Verbindung zu Webnews aufgetreten.';
								$data['post_status'] = 'draft';
								break;								
						}
						
					}
					
				}else{
					delete_post_meta( $post->ID , 'webnewsde_enable_submit' );
				}
			}
			return $data;
		}
		
		// Called after post is saved - save error messages too
		function save_post( $post_ID ) {
			if ( count( $this->errors ) > 0 ) {
				delete_post_meta( $post_ID, 'Webnewsde_msg' );
				add_post_meta( $post_ID, 'Webnewsde_msg', implode( '<br />', $this->errors ), true );
			}
		}
		
		

		function PostRequest($url, $_data) {
			
			$referer = $_SERVER['SERVER_NAME'];
		    // convert variables array to string:
		    $data = array();    
		    while(list($n,$v) = each($_data)){
		        $data[] = "$n=$v";
		    }    
		    $data = implode('&', $data);
		    // format --> test1=a&test2=b etc.
		 
		    // parse the given URL
		    $url = parse_url($url);
		    if ($url['scheme'] != 'http') { 
		        die('Only HTTP request are supported !');
		    }
		 
		    // extract host and path:
		    $host = $url['host'];
		    $path = $url['path'];
		 
		    // open a socket connection on port 80
		    $fp = fsockopen($host, 80);
		 
		    // send the request headers:
		    fputs($fp, "POST $path HTTP/1.1\r\n");
		    fputs($fp, "Host: $host\r\n");
		    fputs($fp, "Referer: $referer\r\n");
		    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		    fputs($fp, "Connection: close\r\n\r\n");
		    fputs($fp, $data);
		 
		    $result = ''; 
		    while(!feof($fp)) {
		        // receive the results of the request
		        $result .= fgets($fp, 128);
		    }
		 
		    // close the socket connection:
		    fclose($fp);
		    
		    // split the result header from the content
		    $result = explode("\r\n\r\n", $result, 2);

		    $header = isset($result[0]) ? trim ( $result[0] ) : '';
		    $content = isset($result[1]) ? trim( $result[1] ) : '';
		    
		    $content =  explode( "\n", $content );
		    if( sizeof($content) == 3 ){
		    	$content = $content[1];		    	
		    }
		    
		    // return as array:
		    return array($header, $content);
		}
	
		// Show meta box on edit post page
		function post_metabox( $post ) {
			
			if( !get_post_meta( $post->ID , 'webnewsde_post_submited', true ) && get_option('webnewsde_settings_enable') == 'on' ){

				$catId = 69;
				if(  get_post_meta( $post->ID  , 'webnewsde_publish_category', true ) > 0 ){
					$catId = get_post_meta( $post->ID  , 'webnewsde_publish_category', true );
				}
?>
<div class="inside">
<!--  Use nonce for verification -->
<!-- The actual fields for data entry -->
<p class="meta-options">
<input <?php if( get_post_meta( $post->ID , 'webnewsde_enable_submit', true ) ): ?>checked="checked"<?php endif; ?>  type="checkbox" name="webnewsde_publish_use" id="webnewsde_publish_use" /><label for="webnewsde_publish_use"> <?php _e('Beitrag bei Webnews veröffentlichen', 'webnewsde'); ?></label>
<!--  <br/>
	<input <?php  if( get_post_meta( $post->ID , 'webnewsde_image_from_content', true ) ): ?>checked="checked"<?php endif; ?>  type="checkbox" name="webnewsde_image_from_content" id="webnewsde_image_from_content" /><label for="webnewsde_image_from_content"> <?php _e('Take image from content if available', 'webnewsde'); ?></label> -->
<br/>
<label>
  <?php  _e('Wähle eine Webnews-Kategorie', 'webnewsde'); 
  
  ?>  : 
<select name="webnewsde_publish_category" id="webnewsde_publish_category"  />
	<?php foreach( $this->news_categories as $cat ): ?>
	<option <?php if( $catId == $cat['catID'] ):?> selected <?php endif;?>  value='<?php echo $cat['catID'] ?>' > <?php echo $cat['catName'] ?> </option>
	<?php endforeach;?>
 </select>

</p>
</div>
<?php
			}else if( get_option('webnewsde_settings_enable') != 'on'){

?>
			<div class="inside">
				<p class="meta-options">
					<?php echo __('Webnews Plugin ist deaktiviert.', 'webnewsde'); ?>
				</p>
			</div>
<?php 
			}else{
?>
			<div class="inside">
				<p class="meta-options">
					<?php echo __('Dein Beitrag wurde erfolgreich bei Webnews verlinkt.', 'webnewsde'); ?>
					<?php $storyId =  get_post_meta( $post->ID , 'storyID', true );
						if( $storyId > 0 ){
							echo ' <a target="_blank" href="http://www.webnews.de/kommentare/' . $storyId . '/0/story_title.html">Link</a>';
						}					
					?>
					
				</p>
			</div>
<?php 
			}
			
			delete_post_meta( $post->ID , 'webnewsde_enable_submit' );
			delete_post_meta( $post->ID  , 'webnewsde_publish_category' );
		}
		
			// Handle options panel
		function options_panel() {
			$message = null;
			if ( isset($_POST['action']) ) {
				check_admin_referer( 'webnewsde-options' );
				$message = __('Deine Konfiguration wurden gespeichert.', 'webnewsde');
				echo '<div id="message" class="updated fade"><p>', $message, '</p></div>', "\n";
			}
?>
<div id="dropmessage" class="updated" style="display:none;"></div>
<div class="wrap">
<h2><?php _e('Webnews.de - Optionen', 'webnewsde'); ?></h2>

<form name="dofollow" action="options.php" method="post">
<?php settings_fields( 'webnewsde' ); ?>
<table class="form-table">

<tr><th colspan="2"><h3><?php _e('Einstellungen:', 'webnewsde'); ?></h3></th></tr>

<tr>
	<th scope="row" style="text-align:right; vertical-align:top;">
		<label for="webnewsde_settings_enable"><?php _e('Plugin aktivieren', 'webnewsde'); ?></label>
	</th>
	<td>
		<input type="checkbox"  id="webnewsde_settings_enable" name="webnewsde_settings_enable" <?php echo get_option( 'webnewsde_settings_enable' ) == 'on' ? 'checked="checked"' : '' ; ?>" />
	</td>
</tr>

<tr>
	<th colspan="2">
		<h3><?php _e('Webnews Anmeldung:', 'webnewsde'); ?></h3>
	</th>
</tr>

<tr >
	<td  colspan="2">
		<h3>Noch kein Profil bei Webnews? Klicke <a target="_blank" href="http://www.webnews.de/registrierung">hier</a>, um dich zu registrieren.</h2>
	</td>
</tr>


<tr>
	<th scope="row" style="text-align:right; vertical-align:top;">
		<label for="webnewsde_settings_username"><?php _e('Webnews Nutzername:', 'webnewsde'); ?></label>
	</th>
	<td>
		<input type="text" maxlength="50" size="30" id="webnewsde_settings_username" name="webnewsde_settings_username" value="<?php echo stripcslashes( get_option( 'webnewsde_settings_username' ) ); ?>" />
	</td>
</tr>

<tr>
	<th scope="row" style="text-align:right; vertical-align:top;">
		<label for="webnewsde_settings_password"><?php _e('Webnews Passwort:', 'webnewsde'); ?></label>
	</th>
	<td>
		<input type="password" maxlength="50" size="30" id="webnewsde_settings_password" name="webnewsde_settings_password" />
	</td>
</tr>

</table> 

<p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="submit" name="Submit" value="<?php _e('Änderungen speichern', 'webnewsde'); ?>" /> 
</p>

</form>
</div>
<?php
		}
		
		function hash_password( $value ) {
			$value = trim($value);
			if( $value != '' ){
				return md5($value);
			}else
				return '';
		}
	
	}	
	
	$wp_webnewsde = new Webnewsde();	

}
?>