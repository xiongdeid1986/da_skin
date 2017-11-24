{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.addfunds desc=$LANG.addfundsintro}

{if $addfundsdisabled}

<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
		<p class="bold">{$LANG.clientareaaddfundsdisabled}</p>
</div>

<br /><br /><br />

{else}

{if $notallowed || $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        <li>{if $notallowed}{$LANG.clientareaaddfundsnotallowed}{else}{$errormessage}{/if}</li>
    </ul>
</div>{/if}

<p>{$LANG.addfundsdescription}</p>
<div class="ticketdetailscontainer">
    <div class="row">
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.addfundsminimum}
				<div class="detail">{$minimumamount}</div>
			</div>
        </div>
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.addfundsmaximum}
				<div class="detail">{$maximumamount}</div>
			</div>
        </div>
		
        <div class="col-sm-4">
            <div class="tickets-internalpadding">
				{$LANG.addfundsmaximumbalance}
				<div class="detail">{$maximumbalance}</div>
			</div>
        </div>
    </div>
</div>
<div class="space-12"></div>
<div class="hr hr32 hr-dotted"></div>

<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=addfunds">
<div class="row">
    <fieldset>
	<div class="col-sm-5">
        <div class="form-group">
		    <label class="col-sm-3 control-label" for="amount">{$LANG.addfundsamount}:</label>
			<div class="col-sm-9">
			    <input type="text" class="input-small" name="amount" id="amount" value="{$amount}" class="input-small" />
			</div>
		</div>
	</div>
	<div class="col-sm-7">
        <div class="form-group">
		    <label class="col-sm-3 control-label" for="paymentmethod">{$LANG.orderpaymentmethod}:</label>
			<div class="col-sm-9">
			    <select class="input-medium" name="paymentmethod" id="paymentmethod">
                {foreach from=$gateways item=gateway}
                    <option value="{$gateway.sysname}">{$gateway.name}</option>
                {/foreach}
                </select>
			</div>
		</div>
	</div>
    </fieldset>
</div>

<br />
<p><span class="label label-warning">Note:</span> {$LANG.addfundsnonrefundable}</p>
	<div class="clearfix form-actions">
		<input type="submit" value="{$LANG.addfunds}" class="btn btn-success" /></p>
	</div>

<br />

</form>
{/if}