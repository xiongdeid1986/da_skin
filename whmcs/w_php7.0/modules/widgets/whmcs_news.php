<?php

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

function widget_whmcs_news($vars) {
    global $whmcs,$_ADMINLANG;

    $title = $_ADMINLANG['home']['whmcsnewsfeed'];

    if ($whmcs->get_req_var('getwhmcsnews')) {
        if (!function_exists("twitter_getTwitterIntents")) {
            include_once(ROOTDIR.'/modules/social/twitter/twitter.php');
            $tweets = array();
        }
        if (function_exists("twitter_getTwitterIntents")) {
            $tweets = twitter_getTwitterIntents('whmcs', WHMCS_Application::getinstance()->getDBVersion());
        }
        $i = 0;
        echo '<div style="float:right;margin:15px 15px 10px 10px;padding:8px 20px;text-align:center;background-color:#FDF8E1;border:1px dashed #FADA5A;-moz-border-radius: 5px;-webkit-border-radius: 5px;-o-border-radius: 5px;border-radius: 5px;">Follow Us<br /><a href="http://twitter.com/whmcs" target="_blank" style="font-size:16px;color:#D9AE06;">@whmcs</a></div>';
        foreach ($tweets AS $values) {
            if ($i>3) break;
            echo '<div style="padding-bottom:5px;">'.$values['tweet'].'<br /><span style="font-size:10px;font-weight:bold;color:#A3A3A3;">'.$values['date'].'</span></div>';
            $i++;
        }
        exit;
    }

    $content = '<div id="whmcsnewsfeed" style="max-height:130px;">'.$vars['loading'].'</div>';

    $jquerycode = '$.post("index.php", { getwhmcsnews: 1 },
    function(data){
        jQuery("#whmcsnewsfeed").html(data);
    });';

    return array('title'=>$title,'content'=>$content,'jquerycode'=>$jquerycode);

}

add_hook("AdminHomeWidgets",1,"widget_whmcs_news");

?>