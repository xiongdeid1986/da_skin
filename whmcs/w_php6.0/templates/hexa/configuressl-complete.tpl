{if $errormessage}

{include file="$template/pageheader.tpl" title=$LANG.sslconfsslcertificate}

<div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>

<input type="button" value="{$LANG.clientareabacklink}" class="btn btn-default btn-sm" onclick="history.go(-1)" />

{else}

{include file="$template/pageheader.tpl" title=$LANG.sslconfigcomplete}

<p>{$LANG.sslconfigcompletedetails}</p>

<form method="post" action="clientarea.php?action=productdetails">
<input type="hidden" name="id" value="{$serviceid}" />
<input type="submit" value="{$LANG.invoicesbacktoclientarea}" class="btn btn-default btn-sm"/>
 </form>

{/if}