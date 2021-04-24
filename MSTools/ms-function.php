<!--
ms-functions.php is the helper file and fuction mstools.php calls to proper display the results
obtained in mstools.php.
-->
<?php 
function load_custom_wp_admin_style() { 
    wp_enqueue_style( 'ms_backend_css', plugins_url('style.css', __FILE__) );
	
	// save current login user login detail in custom metafield
	 $uid=get_current_user_id();
	 $date = date('Y-m-d H:i:s');
	 update_user_meta($uid, 'user_lastlogin',$date);
}

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

?>