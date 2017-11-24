{include file="$template/pageheader.tpl" title=$LANG.upgradedowngradepackage}

<div class="alert alert-block alert-info">
    <p>{$LANG.orderproduct}: <strong>{$groupname} - {$productname}</strong>{if $domain} ({$domain}){/if}</p>
</div>

{if $promoerror}
<div class="alert alert-danger">
    <p>{$promoerror}</p>
</div>
{/if}

<table class="table table-striped table-framed">
    <thead>
        <tr>
            <th>{$LANG.orderdesc}</th>
            <th>{$LANG.orderprice}</th>
        </tr>
    </thead>
    <tbody>
{foreach key=num item=upgrade from=$upgrades}
    {if $type eq "package"}
        <tr>
            <td><input type="hidden" name="pid" value="{$upgrade.newproductid}" /><input type="hidden" name="billingcycle" value="{$upgrade.newproductbillingcycle}" />{$upgrade.oldproductname} => {$upgrade.newproductname}</td>
            <td class="textcenter">{$upgrade.price}</td>
        </tr>
    {elseif $type eq "configoptions"}
        <tr>
            <td>{$upgrade.configname}: {$upgrade.originalvalue} => {$upgrade.newvalue}</td>
            <td class="textcenter">{$upgrade.price}</td>
        </tr>
    {/if}
{/foreach}
        <tr class="subtotal">
            <td class="textright">{$LANG.ordersubtotal}:</td>
            <td class="textcenter">{$subtotal}</td>
        </tr>
        {if $promodesc}
        <tr class="recurring">
            <td class="textright">{$promodesc}:</td>
            <td class="textcenter">{$discount}</td>
        </tr>
        {/if}
        {if $taxrate}
        <tr class="tax">
            <td class="textright">{$taxname} @ {$taxrate}%:</td>
            <td class="textcenter">{$tax}</td>
        </tr>
        {/if}
        {if $taxrate2}
        <tr class="tax">
            <td class="textright">{$taxname2} @ {$taxrate2}%:</td>
            <td class="textcenter">{$tax2}</td>
        </tr>
        {/if}
        <tr class="total">
            <td class="textright">{$LANG.ordertotalduetoday}:</td>
            <td class="textcenter">{$total}</td>
        </tr>
    </tbody>
</table>

{if $promorecurring}
<div class="alert alert-info textcenter">
    <p>{$LANG.recurringpromodesc|sprintf2:$promorecurring}</p>
</div>
{/if}

{if $type eq "package"}
<p>{$LANG.upgradeproductlogic} ({$upgrade.daysuntilrenewal} {$LANG.days})</p>
{/if}

<form method="post" form-inline action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="step" value="2" />
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="id" value="{$id}" />
{if $type eq "package"}
<input type="hidden" name="pid" value="{$upgrades.0.newproductid}" />
<input type="hidden" name="billingcycle" value="{$upgrades.0.newproductbillingcycle}" />
{/if}
{foreach from=$configoptions key=cid item=value}<input type="hidden" name="configoption[{$cid}]" value="{$value}" />{/foreach}
<p><strong>{$LANG.orderpromotioncode}:</strong> {if $promocode}
{$promocode} - {$promodesc} <input type="submit" name="removepromo" value="{$LANG.orderdontusepromo}" class="btn btn-danger" />
{else}
<div class="col-lg-4"><input type="text" name="promocode" class="form-control" /></div><input type="submit" value="{$LANG.orderpromovalidatebutton}" class="btn btn-default" />
{/if}</p>
</form>

<form method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="step" value="3" />
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="id" value="{$id}" />
{if $type eq "package"}
<input type="hidden" name="pid" value="{$upgrades.0.newproductid}" />
<input type="hidden" name="billingcycle" value="{$upgrades.0.newproductbillingcycle}" />
{/if}
{foreach from=$configoptions key=cid item=value}<input type="hidden" name="configoption[{$cid}]" value="{$value}" />{/foreach}
{if $promocode}<input type="hidden" name="promocode" value="{$promocode}">{/if}
{include file="$template/subheader.tpl" title=$LANG.orderpaymentmethod}
<div class="form-group">{foreach key=num item=gateway from=$gateways}<div class="radio-inline"><label><input type="radio" name="paymentmethod" value="{$gateway.sysname}" {if $selectedgateway eq $gateway.sysname} checked{/if}> {$gateway.name} </label></div>{/foreach}</div>
<p><input type="submit" value="{$LANG.ordercontinuebutton}" class="btn btn-primary" /></p>
</form>
