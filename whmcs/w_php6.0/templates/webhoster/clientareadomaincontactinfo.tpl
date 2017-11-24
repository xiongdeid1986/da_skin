{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{literal}
<script language="javascript">
function usedefaultwhois(id) {
	jQuery("."+id.substr(0,id.length-1)+"customwhois").attr("disabled", true);
	jQuery("."+id.substr(0,id.length-1)+"defaultwhois").attr("disabled", false);
	jQuery('#'+id.substr(0,id.length-1)+'1').attr("checked", "checked");
}
function usecustomwhois(id) {
	jQuery("."+id.substr(0,id.length-1)+"customwhois").attr("disabled", false);
	jQuery("."+id.substr(0,id.length-1)+"defaultwhois").attr("disabled", true);
	jQuery('#'+id.substr(0,id.length-1)+'2').attr("checked", "checked");
}
</script>
{/literal}

{include file="$template/pageheader.tpl" title=$LANG.domaincontactinfo}

<div class="alert alert-info">
    <p>{$LANG.domainname}: <strong>{$domain}</strong></p>
</div>

{if $successful}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $error}
    <div class="alert alert-danger">
        <p class="bold text-center">{$error}</p>
    </div>
{/if}

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaincontacts" class="form-horizontal">

<input type="hidden" name="sub" value="save" />
<input type="hidden" name="domainid" value="{$domainid}" />

{foreach from=$contactdetails name=contactdetails key=contactdetail item=values}

<h3><a name="{$contactdetail}"></a>{$contactdetail}{if $smarty.foreach.contactdetails.first}{foreach from=$contactdetails name=contactsx key=contactdetailx item=valuesx}{if !$smarty.foreach.contactsx.first} - <a href="clientarea.php?action=domaincontacts&domainid={$domainid}#{$contactdetailx}">{$LANG.jumpto} {$contactdetailx}</a>{/if}{/foreach}{else} - <a href="clientarea.php?action=domaincontacts&domainid={$domainid}#">{$LANG.top}</a>{/if}</h3>

<div class="well">
	<label class="radio"><input type="radio" class="radio inline" name="wc[{$contactdetail}]" id="{$contactdetail}1" value="contact" onclick="usedefaultwhois(id)"{if $defaultns} checked{/if} /> {$LANG.domaincontactusexisting}</label>
	<label class="radio"><input type="radio" class="radio inline" name="wc[{$contactdetail}]" id="{$contactdetail}2" value="custom" onclick="usecustomwhois(id)"{if !$defaultns} checked{/if} /> {$LANG.domaincontactusecustom}</label>
</div>

<fieldset id="{$contactdetail}defaultwhois">

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="{$contactdetail}3">{$LANG.domaincontactchoose}</label>
		<div class="col-sm-9">
		    <select class="{$contactdetail}defaultwhois" name="sel[{$contactdetail}]" id="{$contactdetail}3" onclick="usedefaultwhois(id)">
            <option value="u{$clientsdetails.userid}">{$LANG.domaincontactprimary}</option>
            {foreach key=num item=contact from=$contacts}
            <option value="c{$contact.id}">{$contact.name}</option>
            {/foreach}
          </select>
		</div>
	</div>

</fieldset>

<fieldset id="{$contactdetail}defaultwhois">
{foreach key=name item=value from=$values}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="{$contactdetail}3">{$contactdetailstranslations.$name}</label>
		<div class="col-sm-9">
		    <input type="text" name="contactdetails[{$contactdetail}][{$name}]" value="{$value}" size="30" class="input-medium {$contactdetail}customwhois" />
		</div>
	</div>
{/foreach}

</fieldset>

{/foreach}

<p><input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" /></p>

</form>

<form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
<input type="hidden" name="id" value="{$domainid}" />
<p><input type="submit" value="{$LANG.clientareabacklink}" class="btn btn-inverse btn-xs" /></p>
</form>
