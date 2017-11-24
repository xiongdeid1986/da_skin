{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.accessdenied}

<div class="alert alert-danger text-center">
    <p><strong>{$LANG.subaccountpermissiondenied}</strong></p>
</div>

<p>{$LANG.subaccountallowedperms}</p>

<br />

<ul>
{foreach from=$allowedpermissions item=permission}
<li>{$permission}</li>
{/foreach}
</ul>

<p>{$LANG.subaccountcontactmaster}</p>

<br />

<p class="text-center"><input type="button" value="{$LANG.clientareabacklink}" onclick="history.go(-1)" class="btn btn-xs btn-inverse" /></p>

<br />
<br />
<br />
<br />