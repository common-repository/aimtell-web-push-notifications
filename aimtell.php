<?php
/*
Plugin Name: Aimtell Push Notifications
Plugin URI: http://wordpress.org/extend/plugins/aimtell-push-notifications/
Description: Send web push notifications. Supported on Safari, Chrome, Firefox and Opera. Plugin enables users to login/register and install required files. Please note this is just an installer and you will need to log into the dashboard to view subscribers and send notifications.
Version: 1.63
Author: Aimtell
Author URI: https://aimtell.com
License: GPL2
*/
/*  
Copyright 2017 Aimtell, Inc.
*/

//set the plugin version to use as a cache buster in css and js
$aimtell_version = "1.63";

// the script requires PHP 5.3+, so this should be defined
if( ! defined('__FOLDERDIR__')){
    define( '__FOLDERDIR__', dirname(__FILE__) );
}

if ( ! defined( 'AIMTELL_URL' ) ) {
    define( 'AIMTELL_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AIMTELL_CURSITE_URL' ) ) {
    define( 'AIMTELL_CURSITE_URL', site_url() );
}

/*************Plugin Functions****************/


function aimtellWP_footer(){
    
    $aimtell_domain = get_option( 'aimtell_domain' );
    $aimtell_uid = get_option( 'aimtell_uid' );
    $aimtell_idSite = get_option( 'aimtell_idSite' );
    $aimtell_webpushid = get_option( 'aimtell_webpushid' );

    //if site is not set up yet, don't show tracking code.
    if(!$aimtell_uid || !$aimtell_idSite || !$aimtell_domain || !$aimtell_webpushid){
        return false;
    }

    //format the website to have no http(s)
    $aimtell_url = explode("//", $aimtell_domain);
    $aimtell_url = $aimtell_url[1];
    $aimtell_manifest_location = wp_make_link_relative(AIMTELL_URL . 'assets/json/aimtell-manifest.json');
    $aimtell_worker_location = wp_make_link_relative(AIMTELL_URL . 'assets/js/aimtell-worker.js.php');

    $aimtell_tracking_code= "<!-- start aimtell tracking code -->       
    <script data-cfasync='false' type='text/javascript'>
     var _at = {};  window._at.track = window._at.track || function(){(window._at.track.q = window._at.track.q || []).push(arguments);}; _at.domain = '{$aimtell_url}'; _at.owner = '{$aimtell_uid}'; _at.idSite = '{$aimtell_idSite}'; _at.webpushid = '{$aimtell_webpushid}'; _at.worker = '{$aimtell_worker_location}'; _at.attributes = {}; (function() { var u='//s3.amazonaws.com/cdn.aimtell.com/trackpush/'; var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'trackpush.min.js'; s.parentNode.insertBefore(g,s); })();
    </script>
    <!-- end aimtell tracking code -->";



    echo $aimtell_tracking_code;

}



/***************Plugin Functions****************/

/****** Admin Functions *********/

function aimtellWP_footer_info() {     
    //load only on plugin page
    if(isset($_GET['page']) && $_GET['page'] == "aimtell-web-push"){
        echo phpversion(); 
    }
} 



function aimtellWP_admin_scripts($hook) {

    //grab the aimtell version
    global $aimtell_version;

    //load only on plugin page
    if(strpos($hook, "aimtell-web-push") > -1 ){

        //load css and set plugin version as cache buster
        wp_enqueue_style( 'aimtell-css', AIMTELL_URL. 'assets/css/stylesheet.css', array(), $aimtell_version );

        //load the aimtell core js file, dependency on jQuery, set plugin version as cache buster
        wp_enqueue_script(
            'aimtell-js',
            AIMTELL_URL . 'assets/js/aimtell.js',
            array( 'jquery' ),
            $aimtell_version
        );
    }
    
}


function aimtellWP_admin_menu() {
    add_menu_page(
        'Aimtell Push',
        'Aimtell Push',
        'manage_options',
        'aimtell-web-push',
        'aimtellWP_admin_load',
        AIMTELL_URL . 'assets/images/aimtell_icon.png'
    );
}


function aimtellWP_admin_load() {
	 
    //make sure user has proper permissions
    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}


    //grab the option vars
    $aimtell_domain = get_option( 'aimtell_domain' );
    $aimtell_uid = get_option( 'aimtell_uid' );
    $aimtell_idSite = get_option( 'aimtell_idSite' );
    $aimtell_webpushid = get_option( 'aimtell_webpushid' );
    
    //grab page 
    $aimtell_show_page = (isset($_POST['page'])) ? $_POST['page'] : null;
   
    //load specific page
    switch ($aimtell_show_page) {

        case 'login':
            include __FOLDERDIR__."/templates/login.php";  
            break;

        case 'addSite':
            include __FOLDERDIR__."/templates/addSite.php";  
            break;

        case 'viewSite':
            
            //if posting idSite for update
            if(!empty($_POST['idSite'])){
                update_option( 'aimtell_idSite', $_POST['idSite'], 'yes' );
            }

            //if posting domain for update
            if(!empty($_POST['domain'])){
                update_option( 'aimtell_domain', $_POST['domain'], 'yes' );
            }

            //if posting webPushID for update
            if(!empty($_POST['webPushID'])){
                update_option( 'aimtell_webpushid', $_POST['webPushID'], 'yes' );
            }

            //if posting webPushID for update
            if(!empty($_POST['uid'])){
                update_option( 'aimtell_uid', $_POST['uid'], 'yes' );
            }

            //grab the token, we need to pass it 
            $aimtell_auth_token = $_COOKIE['aimtell_auth_token'];
            
            include __FOLDERDIR__."/templates/viewSite.php";  
            break;

        default:
            //if auth token is set and we already have all required site variables, go ahead show viewSite
            if(!empty($_COOKIE['aimtell_auth_token']) && !empty($aimtell_domain) && !empty($aimtell_uid) && !empty($aimtell_idSite) && !empty($aimtell_webpushid) ){
                $aimtell_auth_token = $_COOKIE['aimtell_auth_token'];
                include __FOLDERDIR__."/templates/viewSite.php";  
            }
            //if the domain is set in DB, they have an account, show login
            else if(!empty($aimtell_uid)){
                include __FOLDERDIR__."/templates/login.php";
            }
            else{
                include __FOLDERDIR__."/templates/login.php"; //removing register, we are forcing them to register on our website now
            }

            break;
    }

 
}



/************End Admin Functions**************/


add_action( 'admin_menu', 'aimtellWP_admin_menu' );
add_action( 'wp_footer', 'aimtellWP_footer', 100 );
add_action( 'admin_enqueue_scripts', 'aimtellWP_admin_scripts' );
add_filter('admin_footer_text', 'aimtellWP_footer_info'); 

