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
            <p>{$LANG.changessavedsuccessfully}</p>
        </div>
    {/if}
{/if}

<p>{$LANG.domainbulkmanagementchangesaffect}</p>

<br />

<blockquote>
<br />
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
<br />
</blockquote>
<div class="radio">
<label><input type="radio" class="radio inline" name="nschoice" value="default" onclick="disableFields('domnsinputs',true)" checked /> {$LANG.nschoicedefault}</label>
</div>
<div class="radio">
<label><input type="radio" class="radio inline" name="nschoice" value="custom" onclick="disableFields('domnsinputs','')" checked /> {$LANG.nschoicecustom}</label>
</div>
                <fieldset class="control-group">
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="ns1">{$LANG.domainnameserver1}</label>
                        <div class="col-lg-6">
                            <input class="form-control domnsinputs" id="ns1" name="ns1" type="text" value="{$ns1}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="ns2">{$LANG.domainnameserver2}</label>
                        <div class="col-lg-6">
                            <input class="form-control domnsinputs" id="ns2" name="ns2" type="text" value="{$ns2}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="ns3">{$LANG.domainnameserver3}</label>
                        <div class="col-lg-6">
                            <input class="form-control domnsinputs" id="ns3" name="ns3" type="text" value="{$ns3}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="ns4">{$LANG.domainnameserver4}</label>
                        <div class="col-lg-6">
                            <input class="form-control domnsinputs" id="ns4" name="ns4" type="text" value="{$ns4}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label" for="ns5">{$LANG.domainnameserver5}</label>
                        <div class="col-lg-6">
                            <input class="form-control domnsinputs" id="ns5" name="ns5" type="text" value="{$ns5}" />
                        </div>
                    </div>
                    <div class="col-lg-10 col-lg-offset-2">
                    <input type="submit" class="btn btn-primary btn-sm" value="{$LANG.changenameservers}" />
                    <input type="button" value="{$LANG.clientareabacklink}" onclick="window.location='clientarea.php?action=domains'" class="btn btn-default btn-sm" />
                </fieldset>

{elseif $update eq "autorenew"}

{include file="$template/pageheader.tpl" title=$LANG.domainautorenewstatus}

{if $save}
    <div class="alert alert-success">
        {$LANG.changessavedsuccessfully}
    </div>
{/if}

<p>{$LANG.domainautorenewinfo}</p>
<p>{$LANG.domainautorenewrecommend}</p>
<p>{$LANG.domainbulkmanagementchangeaffect}</p>
<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>

<input type="submit" name="enable" value="{$LANG.domainsautorenewenable}" class="btn btn-success " /> &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="disable" value="{$LANG.domainsautorenewdisable}" class="btn btn-danger " />

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
            <p>{$LANG.changessavedsuccessfully}</p>
        </div>
    {/if}
{/if}

<p>{$LANG.domainreglockinfo}</p>
<p>{$LANG.domainreglockrecommend}</p>
<p>{$LANG.domainbulkmanagementchangeaffect}</p>

<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>


<input type="submit" name="enable" value="{$LANG.domainreglockenable}" class="btn btn-success " /> &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="disable" value="{$LANG.domainreglockdisable}" class="btn btn-danger " />

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

<blockquote>
{foreach from=$domains item=domain}
&raquo; {$domain}<br />
{/foreach}
</blockquote>

{foreach from=$contactdetails name=contactdetails key=contactdetail item=values}

<h3><a name="{$contactdetail}"></a>{$contactdetail}</strong>{if $smarty.foreach.contactdetails.first}{foreach from=$contactdetails name=contactsx key=contactdetailx item=valuesx}{if !$smarty.foreach.contactsx.first} - <a href="clientarea.php?action=bulkdomain#{$contactdetailx}">{$LANG.jumpto} {$contactdetailx}</a>{/if}{/foreach}{else} - <a href="clientarea.php?action=bulkdomain#">{$LANG.top}</a>{/if}</h3>

<p></p>

<div class="radio"><label><input type="radio" class="radio inline" name="wc[{$contactdetail}]" id="{$contactdetail}1" value="contact" onclick="usedefaultwhois(id)" /> {$LANG.domaincontactusexisting}</label></div>

<fieldset id="{$contactdetail}defaultwhois">
		<div class="form-group"><div class="col-lg-6">
		    <select class="{$contactdetail}defaultwhois form-control" name="sel[{$contactdetail}]" id="{$contactdetail}3" onclick="usedefaultwhois(id)">
            <option value="u{$clientsdetails.userid}">{$LANG.domaincontactprimary}</option>
            {foreach key=num item=contact from=$contacts}
            <option value="c{$contact.id}">{$contact.name}</option>
            {/foreach}
          </select>
		</div></div>

</fieldset>

<div class="radio"><label><input type="radio" name="wc[{$contactdetail}]" id="{$contactdetail}2" value="custom" onclick="usecustomwhois(id)" checked /> {$LANG.domaincontactusecustom}</label></div>

<fieldset id="{$contactdetail}defaultwhois">

{foreach key=name item=value from=$values}
	    <label for="{$contactdetail}3">{$name}</label>
        <div class="form-group"><div class="col-lg-6">
		    <input type="text" name="contactdetails[{$contactdetail}][{$name}]" value="{$value}" class="{$contactdetail}customwhois form-control" />
		</div>
	</div>
{/foreach}

</fieldset>

{foreachelse}

<div class="alert alert-danger">
{$LANG.domainbulkmanagementnotpossible}
</div>

{/foreach}

<input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" />

{/if}
</form>