{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.domaingeteppcode}

<div class="alert alert-info">
    <p>{$LANG.domainname}: <strong>{$domain}</strong></p>
</div>

<p>{$LANG.domaingeteppcodeexplanation}</p>

<br />

{if $error}
<div class="alert alert-danger">
    {$LANG.domaingeteppcodefailure} {$error}
</div>
{else}
    {if $eppcode}
    <div class="alert alert-warn">
        {$LANG.domaingeteppcodeis}<br /> <span class="label label-important">{$eppcode}</span>
    </div>
    {else}
    <div class="alert alert-warn">
        {$LANG.domaingeteppcodeemailconfirmation}
    </div>
    {/if}
{/if}

<br />

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
	<input type="hidden" name="id" value="{$domainid}" />
	<p><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-xs btn-inverse" /></p>
</form>