<?php
/**
* Plugin Name: WordPress MultiSite (MS) Tools
* Description: Set of tools to help WordPress Super Admins and Network Admins manage their Multisites with increased effectiveness.  
*              My tool displays site url, last login dates, admin email addresses all on one page for simple 
*			   and concise viewing and copying and pasting for WordPress Multisite platforms.  While other tools perform 
*			   similar operations, I've found that the displayment of info across multiple pages and amount of irrelevant info,
*			   were not well suited to my needs as a Super Admin at UC Irvine.  These reasons dictated my need to develop this tool.  
* Version: 2.0
* Author: Mike Huang 
* Author URI: 
* Last Updated: April 2021
**/

add_action('network_admin_menu', 'ms_networkfunc'); 
function ms_networkfunc(){
        add_menu_page( 'MS manager Page', 'MS Tools', 'manage_options', ',ms-dash', 'ms_init', 'lastx');
}
   
require_once('ms-function.php');

function ms_init(){
	
echo "<h1>All Sites</h1>";

//this increases the default of 100 max sites to 10000.  Increase it to whatever you feel like the number of subsites you have on your multisite platform
$args = array('number' => 10000);
//get all the Network sites
$all_sites = get_sites($args);

echo '<table name="ms-sites" style="width:100%;border:solid 1px #ddd;" class="ms-sites-table">';
//echo '<tr><th>Blog ID</th><th>Site Title</th><th>Site Url</th><th>Last Logged user E-mail</th><th>Last Login Date</th><th>Last Modified</th><th>Site Admins</th><th>Emails of Admins</th></tr>';
//Removing the columns that I do not need: Last Logged user Email, Last Modified
echo '<tr><th>Blog ID</th><th>Site Title</th><th>Site Url</th><th>Last Login Date</th><th>Site Admins</th><th>Emails of Admins</th></tr>';



$userids= array();
$site_admins2 = array();

for($i=0;$i<sizeof($all_sites);$i++){
	echo '<tr>';
	switch_to_blog($all_sites[$i]->blog_id);

	$site_id = $blog_id;
	$site_title = get_bloginfo( 'name' );
	$site_admins = get_bloginfo('admin_email');


	//gets email addresses of all the admins on the site

	 $blogAdminUsers = get_users( 'role=Administrator' );

	 foreach ( $blogAdminUsers as $user ) 
	 {
		$str .= '<span>' . esc_html( $user->user_email ) . ', </span>';
	 }

	 $str = rtrim($str, ", </span>");
	 $str .+ '</span>';

	$site_url = get_bloginfo( 'url' );
	
	$get_users = get_users( array( 'blog_id' => $all_sites[$i]->blog_id) );
	$user_id= array();
	$user_ldate = array();
	
	
	// get user's ids of current site 
	foreach($get_users as $value){
		$user_id[] = $value->ID;
		$user_ldate[]=get_user_meta( $value->ID,'user_lastlogin',true);
	}
	
	// get last login date
	$comb_arr=array_combine($user_id,$user_ldate);
		$mostRecent= 0;
		foreach($comb_arr as $date){
			$curDate = strtotime($date);
			if ($curDate > $mostRecent) {
			$mostRecent = $curDate;
			}
		}
	
	$last_ldate=date('Y-m-j H:i:s', $mostRecent);
	
	$last_userid = array_search ($last_ldate,$comb_arr );	
	$author_info = get_user_by('id', $last_userid);
	
	if($last_userid){
		$user_email=$author_info->user_email;	
	}
	
	$splitTimeStamp = explode(" ",$last_ldate);
	
	if($splitTimeStamp[1]=='00:00:00'){
		$last_ldate=$all_sites[$i]->last_updated;
	}

	restore_current_blog();
	echo '<td>'.$all_sites[$i]->blog_id.'</td>';
	echo '<td>'.$site_title.'</td>';
	echo '<td><a href="'.$site_url.'">'.$site_url.'</a></td>';	
	//Remvoing $user_email becuase it outputs to "Last Logged User E-mail" which is not being used.
	//echo '<td>'.$user_email.'</td>';
	
	echo '<td>'.$last_ldate.'</td>';
	
	//Removing all_sites->last_updated becuase it outputs to "Last Logged User E-mail" which is not being used.
	//echo '<td>'.$all_sites[$i]->last_updated.'</td>';

	echo '<td>'.$site_admins.'</td>';
	echo '<td>'.$str.'</td>';

	//echo '<td> <button type="button" onclick="alert(\'Hello world!\')">Click Me!</button> </td>';


	echo '</tr>';

	$str = null;

	}
echo '</table>';
  
}

?>