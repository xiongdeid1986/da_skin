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
@date_default_timezone_set(date_default_timezone_get());
$mtime = gmdate('r', filemtime(__FILE__));
$etag = md5($mtime.__FILE__);
if((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $mtime)
	|| (isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag)) {
	header('HTTP/1.1 304 Not Modified');
	exit();
} else {
	header('ETag: "'.$etag.'"');
	header('Last-Modified: '.$mtime);
	header('Content-Type: text/javascript; charset=UTF-8');
}
?>
var protected_links = "nullrefer.com";
var a_va = 0;
var a_vb = 0;
var a_vc = "";
function auto_anonymize_href()
{
	var a_vd = window.location.hostname;
	if(protected_links != "" && !protected_links.match(a_vd))
	{
		protected_links += ", " + a_vd;
	}
	else if(protected_links == "")
	{
		protected_links = a_vd;
	}
	var a_ve = "";
	var a_vf = new Array();
	var a_vg = 0;
	a_ve = document.getElementsByTagName("a");
	a_va = a_ve.length;
	a_vf = a_fa();
	a_vg = a_vf.length;
	var a_vh = false;
	var j = 0;
	var a_vi = "";
	for(var i = 0; i < a_va; i++)
	{
		a_vh = false;
		j = 0;
		while(a_vh == false && j < a_vg)
		{
			a_vi = a_ve[i].href;
			if(a_vi.match(a_vf[j]) || !a_vi || !(a_vi.match("http://") || a_vi.match("https://")))
			{
				a_vh = true;
			}
			j++;
		}
		if(a_vh == false)
		{
			a_ve[i].href = "http://nullrefer.com/?" + a_vi;
			a_vb++;
			a_vc += i + ":::" + a_ve[i].href + "\n" ;
		}
	}
	var a_vj = document.getElementById("anonyminized");
	var a_vk = document.getElementById("found_links");
	if(a_vj)
	{
		a_vj.innerHTML += a_vb;
	}
	if(a_vk)
	{
		a_vk.innerHTML += a_va;
	}
}
function auto_anonymize_iframe()
{
	var a_vd = window.location.hostname;
	if(protected_links != "" && !protected_links.match(a_vd))
	{
		protected_links += ", " + a_vd;
	}
	else if(protected_links == "")
	{
		protected_links = a_vd;
	}
	var a_ve = "";
	var a_vf = new Array();
	var a_vg = 0;
	a_ve = document.getElementsByTagName("iframe");
	a_va = a_ve.length;
	a_vf = a_fa();
	a_vg = a_vf.length;
	var a_vh = false;
	var j = 0;
	var a_vi = "";
	for(var i = 0; i < a_va; i++)
	{
		a_vh = false;
		j = 0;
		while(a_vh == false && j < a_vg)
		{
			a_vi = a_ve[i].src;
			if(a_vi.match(a_vf[j]) || !a_vi || !(a_vi.match("http://") || a_vi.match("https://")))
			{
				a_vh = true;
			}
			j++;
		}
		if(a_vh == false)
		{
			a_ve[i].src = "http://nullrefer.com/?" + a_vi;
			a_vb++;
			a_vc += i + ":::" + a_ve[i].src + "\n" ;
		}
	}
	var a_vj = document.getElementById("anonyminized");
	var a_vk = document.getElementById("found_links");
	if(a_vj)
	{
		a_vj.innerHTML += a_vb;
	}
	if(a_vk)
	{
		a_vk.innerHTML += a_va;
	}
}
function a_fa()
{
	var a_vf = new Array();
	protected_links = protected_links.replace(" ", "");
	a_vf = protected_links.split(",");
	return a_vf;
}
auto_anonymize_href();
auto_anonymize_iframe();