<div class="halfwidthcontainer">

{include file="$template/pageheader.tpl" title=$LANG.addfunds desc=$LANG.addfundsintro}

{if $addfundsdisabled}

<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaaddfundsdisabled}</p>
</div>

<br /><br /><br />

{else}

{if $notallowed || $errormessage}<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        <li>{if $notallowed}{$LANG.clientareaaddfundsnotallowed}{else}{$errormessage}{/if}</li>
    </ul>
</div>{/if}

<p><small>{$LANG.addfundsdescription}</small></p>

<table class="table table-striped">
    <tbody>
        <tr>
            <td class="textright"><strong>{$LANG.addfundsminimum}</strong></td>
            <td>{$minimumamount}</td>
        </tr>
        <tr>
            <td class="textright"><strong>{$LANG.addfundsmaximum}</strong></td>
            <td>{$maximumamount}</td>
        </tr>
        <tr>
            <td class="textright"><strong>{$LANG.addfundsmaximumbalance}</strong></td>
            <td>{$maximumbalance}</td>
        </tr>
    </tbody>
</table>

<form method="post" action="{$smarty.server.PHP_SELF}?action=addfunds">

<div class="row">
<div class="col-lg-6">
        <div class="form-group">
		    <label class="control-label" for="amount">{$LANG.addfundsamount}:</label>
			<input type="text" class="form-control"  name="amount" id="amount" value="{$amount}" />
            <p class="help-block">{$LANG.addfundsnonrefundable}</p>
			</div>
		</div>

    <div class="col-lg-6">
        <div class="form-group">
		    <label class="control-label" for="paymentmethod">{$LANG.orderpaymentmethod}:</label>
		    <select name="paymentmethod" class="form-control" id="paymentmethod">
                {foreach from=$gateways item=gateway}
                    <option value="{$gateway.sysname}">{$gateway.name}</option>
                {/foreach}
                </select>
			</div>
		</div>
</div>

<input type="submit" value="{$LANG.addfunds}" class="btn btn-default btn-sm pull-right" />

<br />




</form>

{/if}

</div>