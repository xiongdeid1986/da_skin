{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.domaindnsmanagement}

<div class="alert alert-info">
    <p>{$LANG.domainname}: <strong>{$domain}</strong></p>
</div>

<p>{$LANG.domaindnsmanagementdesc}</p>

<br />

{if $error}
<div class="alert alert-danger">
    {$error}
</div>
{/if}

{if $external}

<br /><br />
<div class="text-center">
{$code}
</div>
<br /><br /><br /><br />

{else}
<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=domaindns">
<input type="hidden" name="sub" value="save" />
<input type="hidden" name="domainid" value="{$domainid}" />


<div class="table-responsive">
	<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>{$LANG.domaindnshostname}</th>
            <th>{$LANG.domaindnsrecordtype}</th>
            <th>{$LANG.domaindnsaddress}</th>
            <th>{$LANG.domaindnspriority}</th>
        </tr>
    </thead>
{foreach from=$dnsrecords item=dnsrecord}
        <tr>
            <td><input type="hidden" name="dnsrecid[]" value="{$dnsrecord.recid}" /><input type="text" name="dnsrecordhost[]" value="{$dnsrecord.hostname}" class="input-medium" /></td>
            <td><select name="dnsrecordtype[]" class="input-medium">
<option value="A"{if $dnsrecord.type eq "A"} selected="selected"{/if}>A (Address)</option>
<option value="AAAA"{if $dnsrecord.type eq "AAAA"} selected="selected"{/if}>AAAA (Address)</option>
<option value="MXE"{if $dnsrecord.type eq "MXE"} selected="selected"{/if}>MXE (Mail Easy)</option>
<option value="MX"{if $dnsrecord.type eq "MX"} selected="selected"{/if}>MX (Mail)</option>
<option value="CNAME"{if $dnsrecord.type eq "CNAME"} selected="selected"{/if}>CNAME (Alias)</option>
<option value="TXT"{if $dnsrecord.type eq "TXT"} selected="selected"{/if}>SPF (txt)</option>
<option value="URL"{if $dnsrecord.type eq "URL"} selected="selected"{/if}>URL Redirect</option>
<option value="FRAME"{if $dnsrecord.type eq "FRAME"} selected="selected"{/if}>URL Frame</option>
</select></td>
            <td><input type="text" name="dnsrecordaddress[]" value="{$dnsrecord.address}" class="input-medium" /></td>
            <td>{if $dnsrecord.type eq "MX"}<input type="text" name="dnsrecordpriority[]" value="{$dnsrecord.priority}" class="input-small" />*{else}<input type="hidden" name="dnsrecordpriority[]" value="N/A" />{$LANG.domainregnotavailable}{/if}</td>
        </tr>
{/foreach}
        <tr>
            <td><input type="text" name="dnsrecordhost[]" size="10" class="input-small" /></td>
            <td><select name="dnsrecordtype[]" class="input-medium">
<option value="A">A (Address)</option>
<option value="AAAA">AAAA (Address)</option>
<option value="MXE">MXE (Mail Easy)</option>
<option value="MX">MX (Mail)</option>
<option value="CNAME">CNAME (Alias)</option>
<option value="TXT">SPF (txt)</option>
<option value="URL">URL Redirect</option>
<option value="FRAME">URL Frame</option>
</select></td>
            <td><input type="text" name="dnsrecordaddress[]" class="input-medium" /></td>
            <td><input type="text" name="dnsrecordpriority[]" class="input-small" />*</td>
        </tr>
	</table>
</div>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
	<p>* {$LANG.domaindnsmxonly}</p>
	<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-success" />
	</div>
</div>

</form>
{/if}

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
	<input type="hidden" name="id" value="{$domainid}" />
	<input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-xs btn-inverse" /></p>
</form>