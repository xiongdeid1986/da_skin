{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<form method="post" action="{$smarty.server.PHP_SELF}?action=bulkdomain" class="form-horizontal">
<input type="hidden" name="update" value="{$update}">
<input type="hidden" name="save" value="1">
{foreach from=$domainids item=domainid}
<input type="hidden" name="domids[]" value="{$domainid}" />
{/foreach}

{if $update eq "nameservers"}

{include file="$template/pageheader.tpl" title=$LANG.domainmanagens}

{if $save}
    {if $errors}
        <div class="alert alert-danger">
            <p class="bold">{$LANG.clientareaerrors}</p>
            <ul>
                {foreach from=$errors item=error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </div>
    {else}
        <div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
            <p>{$LANG.changessavedsuccessfully}</p>
        </div>
    {/if}
{/if}

<p>{$LANG.domainbulkmanagementchangesaffect}</p>

<div class="well">
<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>
</div>

<form method="post" action="clientarea.php" class="form-horizontal">
	<input type="hidden" name="action" value="bulkdomain">
	<input type="hidden" name="update" value="nameservers">
	<input type="hidden" name="save" value="1">
	{foreach from=$domainids item=domainid}
	<input type="hidden" name="domids[]" value="{$domainid}">
	{/foreach}
	
	<fieldset name="nschoises">	
		<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-9">
				<label class="radio"><input type="radio" name="nschoice" value="default" onclick="disableFields('domnsinputs',true)"{if $defaultns} checked="checked"{/if}> {$LANG.nschoicedefault}</label>
				<label class="radio"><input type="radio" name="nschoice" value="custom" onclick="disableFields('domnsinputs','')"{if !$defaultns} checked="checked"{/if}> {$LANG.nschoicecustom}</label>
			</div>
		</div>		
	</fieldset>
	
	<div class="hr hr32 hr-dotted"></div>
	
	<fieldset name="nameservers">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="ns1">{$LANG.domainnameserver1}</label>
			<div class="col-sm-9">
				<input class="col-xs-10 col-sm-4 domnsinputs" id="ns1" name="ns1" type="text" value="{$ns1}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="ns2">{$LANG.domainnameserver2}</label>
			<div class="col-sm-9">
				<input class="col-xs-10 col-sm-4 domnsinputs" id="ns2" name="ns2" type="text" value="{$ns2}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="ns3">{$LANG.domainnameserver3}</label>
			<div class="col-sm-9">
				<input class="col-xs-10 col-sm-4 domnsinputs" id="ns3" name="ns3" type="text" value="{$ns3}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="ns4">{$LANG.domainnameserver4}</label>
			<div class="col-sm-9">
				<input class="col-xs-10 col-sm-4 domnsinputs" id="ns4" name="ns4" type="text" value="{$ns4}">
			</div>
		</div>
		<div class="form-group">			
			<label class="col-sm-3 control-label" for="ns5">{$LANG.domainnameserver5}</label>
			<div class="col-sm-9">
				<input class="col-xs-10 col-sm-4 domnsinputs" id="ns5" name="ns5" type="text" value="{$ns5}">
			</div>
		</div>
	</fieldset>
	<div class="clearfix form-actions">
		<div class="col-md-offset-3 col-md-9">
			<input type="submit" class="btn btn-info btn-sm" onclick="$('#modalpleasewait').modal();" value="{$LANG.changenameservers}">
		</div>
	</div>
</form>

{elseif $update eq "autorenew"}

{include file="$template/pageheader.tpl" title=$LANG.domainautorenewstatus}

{if $save}
    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
        <p>{$LANG.changessavedsuccessfully}</p>
    </div>
{/if}

<p>{$LANG.domainautorenewinfo}</p>
    <div class="alert alert-info">
		<p>{$LANG.domainautorenewrecommend}</p>
	</div>
<p>{$LANG.domainbulkmanagementchangeaffect}</p>

<div class="well">
<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>
</div>

<p class="hidden-xs visible-sm visible-lg visible-md"><input type="submit" name="enable" value="{$LANG.domainsautorenewenable}" class="btn btn-success" /> &nbsp;&nbsp;<input type="submit" name="disable" value="{$LANG.domainsautorenewdisable}" class="btn btn-danger" /></p>
<p class="visible-xs hidden-sm hidden-lg hidden-md"><input type="submit" name="enable" value="{$LANG.domainsautorenewenable}" class="btn btn-success btn-xs" /> &nbsp;&nbsp;<input type="submit" name="disable" value="{$LANG.domainsautorenewdisable}" class="btn btn-danger btn-xs" /></p>

{elseif $update eq "reglock"}

{include file="$template/pageheader.tpl" title=$LANG.domainreglockstatus}

{if $save}
    {if $errors}
        <div class="alert alert-danger">
            <p class="bold">{$LANG.clientareaerrors}</p>
            <ul>
                {foreach from=$errors item=error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </div>
    {else}
        <div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
            <p>{$LANG.changessavedsuccessfully}</p>
        </div>
    {/if}
{/if}

<p>{$LANG.domainreglockinfo}</p>
        <div class="alert alert-info">
			{$LANG.domainreglockrecommend}
		</div>
<p>{$LANG.domainbulkmanagementchangeaffect}</p>

<div class="well">
<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>
</div>

<p class="hidden-xs visible-sm visible-lg visible-md"><input type="submit" name="enable" value="{$LANG.domainreglockenable}" class="btn btn-success" /> &nbsp;&nbsp;<input type="submit" name="disable" value="{$LANG.domainreglockdisable}" class="btn btn-danger" /></p>
<p class="visible-xs hidden-sm hidden-lg hidden-md"><input type="submit" name="enable" value="{$LANG.domainreglockenable}" class="btn btn-success btn-xs" /> &nbsp;&nbsp;<input type="submit" name="disable" value="{$LANG.domainreglockdisable}" class="btn btn-danger btn-xs" /></p>

{elseif $update eq "contactinfo"}

{include file="$template/pageheader.tpl" title=$LANG.domaincontactinfoedit}

{if $save}
    {if $errors}
        <div class="alert alert-danger">
            <p class="bold">{$LANG.clientareaerrors}</p>
            <ul>
                {foreach from=$errors item=error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </div>
    {else}
        <div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
            <p>{$LANG.changessavedsuccessfully}</p>
        </div>
    {/if}
{/if}

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

<p>{$LANG.domainbulkmanagementchangesaffect}</p>

<div class="well">
<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>
</div>

{foreach from=$contactdetails name=contactdetails key=contactdetail item=values}

<h3><a name="{$contactdetail}"></a>{$contactdetail}</strong>{if $smarty.foreach.contactdetails.first}{foreach from=$contactdetails name=contactsx key=contactdetailx item=valuesx}{if !$smarty.foreach.contactsx.first} - <a href="clientarea.php?action=bulkdomain#{$contactdetailx}">{$LANG.jumpto} {$contactdetailx}</a>{/if}{/foreach}{else} - <a href="clientarea.php?action=bulkdomain#">{$LANG.top}</a>{/if}</h3>

<fieldset class="well control-group">
	<label class="radio"><input type="radio" class="radio inline" name="wc[{$contactdetail}]" id="{$contactdetail}1" value="contact" onclick="usedefaultwhois(id)" /> {$LANG.domaincontactusexisting}</label>
	<label class="radio"><input type="radio" class="radio inline" name="wc[{$contactdetail}]" id="{$contactdetail}2" value="custom" onclick="usecustomwhois(id)" checked /> {$LANG.domaincontactusecustom}</label>
</fieldset>

<fieldset class="onecol" id="{$contactdetail}defaultwhois">

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
<fieldset class="onecol" id="{$contactdetail}defaultwhois">

{foreach key=name item=value from=$values}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="{$contactdetail}3">{$name}</label>
		<div class="col-sm-9">
		    <input type="text" name="contactdetails[{$contactdetail}][{$name}]" value="{$value}" size="30" class="{$contactdetail}customwhois" />
		</div>
	</div>
{/foreach}

</fieldset>

{foreachelse}

<div class="alert alert-danger">
    <p>{$LANG.domainbulkmanagementnotpossible}</p>
</div>

{/foreach}

<p><input type="submit" value="{$LANG.clientareasavechanges}" onclick="$('#modalpleasewait').modal();" class="btn btn-info" /></p>
{/if}
</form>

<p><input type="button" value="{$LANG.clientareabacklink}" onclick="window.location='clientarea.php?action=domains'" class="btn btn-inverse btn-xs" /></p>
<br /><br />

<div class="modal hide fade in" id="modalpleasewait">
	<div class="modal-header text-center">
		<h3><img src="images/loadingsml.gif" alt="{$LANG.pleasewait}" class="valignbaseline"> {$LANG.pleasewait}</h3>
	</div>
</div>