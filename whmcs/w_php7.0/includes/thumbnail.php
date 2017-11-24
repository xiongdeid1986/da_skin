<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
require("../init.php");
error_reporting(0);
if( !function_exists('getimagesize') )
{
    exit( "You need to recompile with the GD library included in PHP for this feature to be able to function" );
}
$filename = '';
if( $tid )
{
    $data = get_query_vals('tbltickets', 'userid,attachment', array( 'id' => $tid ));
    $userid = $data[0];
    $attachments = $data[1];
    $attachments = explode("|", $attachments);
    $filename = $attachments_dir . $attachments[$i];
}
if( $rid )
{
    $data = get_query_vals('tblticketreplies', 'tid,attachment', array( 'id' => $rid ));
    $ticketid = $data[0];
    $attachments = $data[1];
    $attachments = explode("|", $attachments);
    $filename = $attachments_dir . $attachments[$i];
    $userid = get_query_val('tbltickets', 'userid', array( 'id' => $ticketid ));
}
if( $_SESSION['uid'] != $userid && !$_SESSION['adminid'] )
{
    $filename = ROOTDIR . "/images/nothumbnail.gif";
}
if( !$filename )
{
    $filename = ROOTDIR . "/images/nothumbnail.gif";
}
$size = getimagesize($filename);
switch( $size['mime'] )
{
    case 'image/jpeg':
        $img = imagecreatefromjpeg($filename);
        break;
    case 'image/gif':
        $img = imagecreatefromgif($filename);
        break;
    case 'image/png':
        $img = imagecreatefrompng($filename);
        break;
    default:
        $img = false;
        break;
}
$thumbWidth = 200;
$thumbHeight = 125;
if( !$img )
{
    $filename = ROOTDIR . "/images/nothumbnail.gif";
    $img = imagecreatefromgif($filename);
}
$width = imagesx($img);
$height = imagesy($img);
$new_width = $thumbWidth;
$new_height = floor($height * $thumbWidth / $width);
if( $thumbHeight < $new_height )
{
    $new_height = $thumbHeight;
    $new_width = floor($width * $thumbHeight / $height);
}
$tmp_img = imagecreatetruecolor($new_width, $new_height);
imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: " . $size['mime']);
imagejpeg($tmp_img);
imagedestroy($tmp_img);