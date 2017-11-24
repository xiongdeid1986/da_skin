{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.domainemailforwarding}

<div class="alert alert-info">
    <p>{$LANG.domainname}: <strong>{$domain}</strong></p>
</div>

<p>{$LANG.domainemailforwardingdesc}</p>

<br />

{if $error}
<div class="alert alert-danger">
    {$error}
</div>
{/if}

{if $external}

<br /><br />
<div class="textcenter">
{$code}
</div>
<br /><br /><br /><br />

{else}
<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=domainemailforwarding">
<input type="hidden" name="sub" value="save" />
<input type="hidden" name="domainid" value="{$domainid}" />

<div class="table-responsive"><table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>{$LANG.domainemailforwardingprefix}</th>
            <th></th>
            <th>{$LANG.domainemailforwardingforwardto}</th>
        </tr>
    </thead>
{foreach key=num item=emailforwarder from=$emailforwarders}
        <tr>
            <td><input type="text" name="emailforwarderprefix[{$num}]" value="{$emailforwarder.prefix}" class="input-small" /></td>
            <td>@{$domain} => </td>
            <td><input type="text" name="emailforwarderforwardto[{$num}]" value="{$emailforwarder.forwardto}" class="input-small" /></td>
        </tr>
{/foreach}
        <tr>
            <td><input type="text" name="emailforwarderprefixnew" class="input-medium" /></td>
            <td>@{$domain} => </td>
            <td><input type="text" name="emailforwarderforwardtonew" class="input-medium" /></td>
        </tr>
</table></div>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-success" />
	</div>
</div>

</form>
{/if}

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
	<input type="hidden" name="id" value="{$domainid}" />
	<p><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-xs btn-inverse" /></p>
</form>