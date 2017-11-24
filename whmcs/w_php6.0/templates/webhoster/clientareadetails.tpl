{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>

{include file="$template/pageheader.tpl" title=$LANG.clientareanavdetails}
{include file="$template/clientareadetailslinks.tpl"}

{if $successful}
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=details">

<fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="firstname">{$LANG.clientareafirstname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="firstname" id="firstname" value="{$clientfirstname}"{if in_array('firstname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="lastname">{$LANG.clientarealastname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="lastname" id="lastname" value="{$clientlastname}"{if in_array('lastname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="companyname">{$LANG.clientareacompanyname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="companyname" id="companyname" value="{$clientcompanyname}"{if in_array('companyname',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="email">{$LANG.clientareaemail}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="email" id="email" value="{$clientemail}"{if in_array('email',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>
	
	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="blank">&nbsp;</label>
		<div class="col-sm-9">
		    &nbsp;
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="paymentmethod">{$LANG.paymentmethod}</label>
		<div class="col-sm-9">
		    <select class="col-xs-12 col-sm-4" name="paymentmethod" id="paymentmethod">
            <option value="none">{$LANG.paymentmethoddefault}</option>
            {foreach from=$paymentmethods item=method}
            <option value="{$method.sysname}"{if $method.sysname eq $defaultpaymentmethod} selected="selected"{/if}>{$method.name}</option>
            {/foreach}
            </select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="billingcontact">{$LANG.defaultbillingcontact}</label>
		<div class="col-sm-9">
		    <select class="col-xs-12 col-sm-4" name="billingcid" id="billingcontact">
            <option value="0">{$LANG.usedefaultcontact}</option>
            {foreach from=$contacts item=contact}
            <option value="{$contact.id}"{if $contact.id eq $billingcid} selected="selected"{/if}>{$contact.name}</option>
            {/foreach}
            </select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address1">{$LANG.clientareaaddress1}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="address1" id="address1" value="{$clientaddress1}"{if in_array('address1',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address2">{$LANG.clientareaaddress2}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="address2" id="address2" value="{$clientaddress2}"{if in_array('address2',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="city">{$LANG.clientareacity}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="city" id="city" value="{$clientcity}"{if in_array('city',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="state">{$LANG.clientareastate}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="state" id="state" value="{$clientstate}"{if in_array('state',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="postcode">{$LANG.clientareapostcode}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="postcode" id="postcode" value="{$clientpostcode}"{if in_array('postcode',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="country">{$LANG.clientareacountry}</label>
		<div class="col-sm-9">
		    {$clientcountriesdropdown}
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="phonenumber">{$LANG.clientareaphonenumber}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="phonenumber" id="phonenumber" value="{$clientphonenumber}"{if in_array('phonenumber',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>
    {if $emailoptoutenabled}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="emailoptout">{$LANG.emailoptout}</label>
		<div class="col-sm-9">
			<div class="tcb">
				<label>
					<input type="checkbox" class="tc tc-green" value="1" name="emailoptout" id="emailoptout" {if $emailoptout} checked{/if} />
					<span class="labels"> {$LANG.emailoptoutdesc}</span>
				</label>
			</div>
		</div>
	</div>
    {/if}

{if $customfields}
{foreach from=$customfields key=num item=customfield}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="customfield{$customfield.id}">{$customfield.name}</label>
		<div class="col-sm-9">
		    {$customfield.input} {$customfield.description}
		</div>
	</div>
{/foreach}
{/if}

</fieldset>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="save" value="{$LANG.clientareasavechanges}" />
		<input class="btn" type="reset" value="{$LANG.cancel}" />
	</div>
</div>

</form>