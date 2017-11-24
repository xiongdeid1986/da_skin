{include file="$template/pageheader.tpl" title=$LANG.domaindnsmanagement}
<p>
<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
<input type="hidden" name="id" value="{$domainid}" />
<input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-default" />
</form>
</p>
<blockquote>{$LANG.domainname}: {$domain}</blockquote>
<p>{$LANG.domaindnsmanagementdesc}</p>
{if $error}
<div class="alert alert-danger">
    {$error}
</div>
{/if}
{if $external}
<p>{$code}</p>
{else}
<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=domaindns">
<input type="hidden" name="sub" value="save" />
<input type="hidden" name="domainid" value="{$domainid}" />
<div class="row">
<div class="col-lg-12">
<table class="table table-condensed">
    <thead>
        <tr>
            <th class="textcenter">{$LANG.domaindnshostname}</th>
            <th class="textcenter">{$LANG.domaindnsrecordtype}</th>
            <th class="textcenter">{$LANG.domaindnsaddress}</th>
            <th class="textcenter">{$LANG.domaindnspriority} *</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$dnsrecords item=dnsrecord}
        <tr>
            <td><input type="hidden" name="dnsrecid[]" value="{$dnsrecord.recid}" /><input type="text" name="dnsrecordhost[]" value="{$dnsrecord.hostname}" class="form-control input-sm" /></td>
            <td><select class="form-control input-sm" name="dnsrecordtype[]">
<option value="A"{if $dnsrecord.type eq "A"} selected="selected"{/if}>A (Address)</option>
<option value="AAAA"{if $dnsrecord.type eq "AAAA"} selected="selected"{/if}>AAAA (Address)</option>
<option value="MXE"{if $dnsrecord.type eq "MXE"} selected="selected"{/if}>MXE (Mail Easy)</option>
<option value="MX"{if $dnsrecord.type eq "MX"} selected="selected"{/if}>MX (Mail)</option>
<option value="CNAME"{if $dnsrecord.type eq "CNAME"} selected="selected"{/if}>CNAME (Alias)</option>
<option value="TXT"{if $dnsrecord.type eq "TXT"} selected="selected"{/if}>SPF (txt)</option>
<option value="URL"{if $dnsrecord.type eq "URL"} selected="selected"{/if}>URL Redirect</option>
<option value="FRAME"{if $dnsrecord.type eq "FRAME"} selected="selected"{/if}>URL Frame</option>
</select></td>
            <td><input type="text" name="dnsrecordaddress[]" value="{$dnsrecord.address}" class="form-control input-sm" /></td>
            <td>{if $dnsrecord.type eq "MX"}<input type="text" name="dnsrecordpriority[]" value="{$dnsrecord.priority}" class="form-control input-sm" />{else}<input type="hidden" name="dnsrecordpriority[]" value="N/A" />{$LANG.domainregnotavailable}{/if}</td>
        </tr>
{/foreach}
        <tr>
            <td><input type="text" name="dnsrecordhost[]" class="form-control input-sm" /></td>
            <td><select name="dnsrecordtype[]" class="form-control input-sm">
<option value="A">A (Address)</option>
<option value="AAAA">AAAA (Address)</option>
<option value="MXE">MXE (Mail Easy)</option>
<option value="MX">MX (Mail)</option>
<option value="CNAME">CNAME (Alias)</option>
<option value="TXT">SPF (txt)</option>
<option value="URL">URL Redirect</option>
<option value="FRAME">URL Frame</option>
</select></td>
            <td><input type="text" name="dnsrecordaddress[]" class="form-control input-sm" /></td>
            <td><input type="text" name="dnsrecordpriority[]" class="form-control input-sm" /></td>
        </tr>
    </tbody>
</table>
</div>
</div>
<p>* {$LANG.domaindnsmxonly}</p>
<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />
</form>
{/if}
