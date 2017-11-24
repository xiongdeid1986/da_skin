{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{if $errormessage}

{include file="$template/pageheader.tpl" title=$LANG.sslconfsslcertificate}

<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>

<p><input type="button" value="{$LANG.clientareabacklink}" class="btn btn btn-xs btn-inverse" onclick="history.go(-1)" /></p>

{else}

{include file="$template/pageheader.tpl" title=$LANG.sslconfigcomplete}

<p>{$LANG.sslconfigcompletedetails}</p>

<br />

<form method="post" action="clientarea.php?action=productdetails">
<input type="hidden" name="id" value="{$serviceid}" />
<p><input type="submit" value="{$LANG.invoicesbacktoclientarea}" class="btn btn-xs btn-inverse"/></p>
 </form>

{/if}

<br />
<br />
<br />