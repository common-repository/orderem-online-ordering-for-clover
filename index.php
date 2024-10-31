<?php
/*
Plugin Name:  Online Ordering Plus Custom Branded Apps For Clover Merchants
Plugin URI:   OrderEm.com
Description:  OrderEm - Receive Online Orders to your Clover POS from your WordPress website
Version:      1.0
Author:       OrderEm.com
Author URI:   https://orderem.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

// Specify Hooks/Filters
register_activation_hook(__FILE__, 'munch_em_insert_page');
add_action('admin_menu', 'munch_add_page_fn');
add_action('admin_menu', 'munch_update_page');

//de-activate hooks
register_deactivation_hook(__FILE__, 'munch_em_delete_page');
register_deactivation_hook(__FILE__, 'munch_em_delete_data');

// Define default option settings

function munch_em_insert_page(){
	$munch_def_content = '<h3>Please Update Munchem Settings at <b><i>SETTINGS > MUNCHEM SETTINGS</i></b>';
	$plugin_dir_path = plugin_dir_url(__FILE__);
    // Define my page arguments
   $page = array(
        'post_title'   => 'ORDER NOW',
        'post_content' => $munch_def_content,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_type'    => 'page',
    );

    wp_insert_post( $page, '' );
}


// deletes menu page upon plugin de-activate
function munch_em_delete_page(){
    $page1 = get_page_by_title( 'ORDER NOW' );

    // Force delete this so the Title/slug "ORDER NOW" can be used again.
    wp_delete_post( $page1->ID, true );
}

function munch_em_delete_data() {
		delete_option('munch_plugin_options');
}

// Register our settings. Add the settings section, and settings fields
function munch_init_fn(){
	
	register_setting('munch_plugin_options', 'munch_plugin_options', 'munch_ulr_validate');
	add_settings_section('main_section', 'Configure Settings to Receive Online Ordering From The Website', 'munch_section_text_fn', __FILE__);
	add_settings_field('munch_url', 'URL', 'munch_setting_url_fn', __FILE__, 'main_section');	
	
}

// Add sub page to the Settings Menu
function munch_add_page_fn() {
	add_options_page('Clover POS Orders', 'Clover POS Orders', 'administrator', __FILE__, 'munch_options_page_fn');
}

// ************************************************************************************************************

// Callback functions

// Section HTML, displayed before the first option
function  munch_section_text_fn() {
	echo '<p>Configure your clover integration steps using OrderEm.com including import POS menu, hours. Once configured, plugin the restaurant URL provided by OrderEm</p>';
}

// TEXTBOX - Name: plugin_options[text_string]
function munch_setting_url_fn() {
	$options = get_option('munch_plugin_options');
	echo "<input id='munch_url' name='munch_plugin_options[munch_url]' size='40' required='required' type='text' value='{$options['munch_url']}' />";
}


// CHECKBOX - Name: plugin_options[chkbox1]
function munch_setting_chk1_fn() {
	$options = get_option('munch_plugin_options');
	if($options['chkbox1']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='plugin_chk1' name='plugin_options[chkbox1]' type='checkbox' />";
}
// Display the admin options page
function munch_options_page_fn() {
	if (!current_user_can('administrator'))  {
    wp_die( __('You do not have sufficient pilchards to access this page.')    );
  }
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<form action="options.php" method="POST" name="options">
		<?php settings_fields('munch_plugin_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="submit" id="submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save'); ?>" />
		</p>
		</form>
		<ol>
		<li>Go to <a target="_blank" href="https://clover.com/appmarket">Clover App Market</a> and search for <b>OrderEm</b>.</li> 
		<li>Download OrderEm app to install and sign up </li>
		<li>Login to OrderEm.com and follow the steps </li>
		<li>Go to Left Handside Navigation Tree   Restaurant -> Location -> Online Ordering Integration </li>
		<li>Select "Call the URL value" to copy </li>
		<li>Enter URL from OrderEm - Online Ordering Integration Tab. </li>
		<li>Clive "Save" button. </li>
		<li>Go to <a href="nav-menus.php">MENU</a> and add "ORDER NOW" page to your MAIN MENU.</li>
		</ol>
	</div>
<?php
if (isset($_POST['generate'])){
	if ( 
    ! isset( $_POST['munch_nonce_verify'] ) 
    || ! wp_verify_nonce( $_POST['munch_nonce_verify'], 'munch_nonce' ) 
) {

   print 'Sorry, your nonce did not verify.';
   exit;

} else {

   munch_update_page();
}
} 
}

function munch_update_page()
{
		$plugin_dir_path = plugin_dir_url(__FILE__);
		$munch_get_values = get_option('munch_plugin_options');
		//$munch_height = $munch_get_values['munch_height'];
		$munch_url = $munch_get_values['munch_url'];
		if($munch_url != ""){
		$str = '';
		
	$str .= '<iframe class="iframe" src="'.$munch_url.'" width="100%" height="1200px" frameborder="0" hspace="0" vspace="0" marginheight="0" marginwidth="0"></iframe>';  
	$plugin_dir_path = plugin_dir_url(__FILE__);
	$my_page = get_page_by_title( 'ORDER NOW' );
	
    // Define my page arguments
   $page = array(
        'post_title'   => 'ORDER NOW',
		'ID' 		   => $my_page->ID,
        'post_content' => $str,
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
        'post_type'    => 'page',
    );

    wp_update_post( $page, true );
	if (is_wp_error($page)) {
	$errors = $page->get_error_messages();
	foreach ($errors as $error) {
		
	}
}
}
}


// Validate user data for some/all of your input fields
function munch_ulr_validate($munch_ulr) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	
	$munch_ulr['munch_url'] =  wp_filter_nohtml_kses($munch_ulr['munch_url']);
	if (strpos($munch_ulr['munch_url'], 'munchem.com') !== false) {
    return $munch_ulr; //return validated input
	var_dump($munch_ulr);
}else{
	$munch_ulr = null;
	 add_settings_error(
        'input_null_error',
        esc_attr( 'Invalid URL' ),
        'Please Enter Valid MUNCHEM.COM URL',
        'error'
    );
}
	
}

	
add_action('admin_init', 'munch_init_fn' );

