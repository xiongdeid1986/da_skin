{include file="$template/pageheader.tpl" title=$LANG.sslconfsslcertificate}

{if $errormessage}
<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

{include file="$template/subheader.tpl" title=$LANG.sslcertinfo}

<div class="row">
<div class="col-md-6">
    <h4>{$LANG.sslcerttype}:</h4><p>{$certtype}</p>
</div>
<div class="col-md-6">
    <h4>{$LANG.sslorderdate}:</h4><p>{$date}</p>
</div>
{if $domain}
<div class="col-md-6">
    <h4>{$LANG.domainname}:</h4><p>{$domain}</p>
</div>{/if}
<div class="col-md-6">
    <h4>{$LANG.orderprice}:</h4><p>{$price}</p>
</div>
<div class="col-md-6">
    <h4>{$LANG.sslstatus}:</h4><p>{$status}</p>
</div>
{foreach from=$displaydata key=displaydataname item=displaydatavalue}
<div class="col-md-6">
    <h4>{$displaydataname}:</h4><p>{$displaydatavalue}</p>
</div>
{/foreach}
</div>

<form method="post" action="{$smarty.server.PHP_SELF}?cert={$cert}&step=3">
{include file="$template/subheader.tpl" title=$LANG.sslcertapproveremail}
<p>{$LANG.sslcertapproveremaildetails}</p>
<fieldset>
        <div class="form-group">
	    <label for="servertype">{$LANG.sslcertapproveremail}</label>
            {foreach from=$approveremails item=approveremail key=num}
            <div class="col-md-6">
            <div class="radio"><label><input type="radio" class="radio inline" name="approveremail" value="{$approveremail}"{if $num eq 0} checked{/if} /> <span>{$approveremail}</span></label></div>
            </div>
            {/foreach}
		</div>
</fieldset>

<input type="submit" value="{$LANG.ordercontinuebutton}" class="btn btn-primary" />

</form>