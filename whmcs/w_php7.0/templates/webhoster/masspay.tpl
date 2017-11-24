{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.masspaytitle desc=$LANG.masspayintro}

<form method="post" action="clientarea.php?action=masspay" class="form-horizontal">
<input type="hidden" name="geninvoice" value="true" />

<br />

<table class="table table-striped table-bordered tc-table">
    <thead>
        <tr>
            <th>{$LANG.invoicesdescription}</th>
            <th width="90px">{$LANG.invoicesamount}</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$invoiceitems key=invid item=invoiceitem}
        <tr>
            <td colspan="2">
                <strong>{$LANG.invoicenumber} {$invid}</strong>
                <input type="hidden" name="invoiceids[]" value="{$invid}" />
            </td>
        </tr>
{foreach from=$invoiceitem item=item}
        <tr>
            <td>{$item.description}</td>
            <td width="90px">{$item.amount}</td>
        </tr>
{/foreach}
{foreachelse}
        <tr>
            <td colspan="6" align="text-canter">{$LANG.norecordsfound}</td>
        </tr>
{/foreach}
        <tr class="subtotal">
            <td class="text-right">{$LANG.invoicessubtotal}:</td>
            <td>{$subtotal}</td>
        </tr>
        {if $tax}<tr class="text-danger">
            <td class="text-right">{$LANG.invoicestax}:</td>
            <td>{$tax}</td>
        </tr>{/if}
        {if $tax2}<tr class="text-danger">
            <td class="text-right">{$LANG.invoicestax} 2:</td>
            <td>{$tax2}</td>
        </tr>{/if}
        {if $credit}<tr class="text-success">
            <td class="text-right"><i class="fa fa-arrow-circle-o-right"></i> {$LANG.invoicescredit}:</td>
            <td>{$credit}</td>
        </tr>{/if}
        {if $partialpayments}<tr class="text-success">
            <td class="text-right">{$LANG.invoicespartialpayments}:</td>
            <td>{$partialpayments}</td>
        </tr>{/if}
        <tr class="text-danger">
            <td class="text-right">{$LANG.invoicestotaldue}:</td>
            <td>{$total}</td>
        </tr>
    </tbody>
</table>

<h4>{$LANG.orderpaymentmethod}</h4>
<hr />
	<select name="paymentmethod" class="input-medium">
		{foreach from=$gateways key=num item=gateway}
			<option value="{$gateway.sysname}"{if $gateway.sysname eq $defaultgateway} selected="selected"{/if}>{$gateway.name}</option>
		{/foreach}
	</select>

<br />
<br />

<p><input type="submit" value="{$LANG.masspaymakepayment}" class="btn btn-primary" /></p>

<br />
<br />
<br />

</form>