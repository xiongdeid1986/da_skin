{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


{if $contactid}

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
{include file="$template/pageheader.tpl" title=$LANG.clientareanavcontacts}
{include file="$template/clientareadetailslinks.tpl"}

{if $successful}
<div class="alert alert-success">
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{/if}

{if $errormessage}
<div class="alert alert-danger">
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<script type="text/javascript">
{literal}
jQuery(document).ready(function(){
    jQuery("#subaccount").click(function () {
        if (jQuery("#subaccount:checked").val()!=null) {
            jQuery("#subaccountfields").slideDown();
        } else {
            jQuery("#subaccountfields").slideUp();
        }
    });
});
{/literal}
function deleteContact() {ldelim}
if (confirm("{$LANG.clientareadeletecontactareyousure}")) {ldelim}
window.location='clientarea.php?action=contacts&delete=true&id={$contactid}&token={$token}';
{rdelim}{rdelim}
</script>

<form method="post" action="{$smarty.server.PHP_SELF}?action=contacts" class="form-search">
<div class="note">
<h4>{$LANG.clientareachoosecontact}</h4>
<select class="col-sm-2" name="contactid" onchange="submit()">
    {foreach item=contact from=$contacts}
        <option value="{$contact.id}"{if $contact.id eq $contactid} selected="selected"{/if}>{$contact.name}</option>
    {/foreach}
    <option value="new">{$LANG.clientareanavaddcontact}</option>
    </select> <input class="btn btn-success btn-sm" type="submit" value="{$LANG.go}" />
	</div>
</form>

<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=contacts&id={$contactid}">

<fieldset>


    <div class="form-group">
	    <label class="col-sm-3 control-label" for="firstname">{$LANG.clientareafirstname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="firstname" id="firstname" value="{$contactfirstname}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="lastname">{$LANG.clientarealastname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="lastname" id="lastname" value="{$contactlastname}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="companyname">{$LANG.clientareacompanyname}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="companyname" id="companyname" value="{$contactcompanyname}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="email">{$LANG.clientareaemail}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="email" id="email" value="{$contactemail}" />
		</div>
	</div>
	
	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address1">{$LANG.clientareaaddress1}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-8" name="address1" id="address1" value="{$contactaddress1}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="address2">{$LANG.clientareaaddress2}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-8" name="address2" id="address2" value="{$contactaddress2}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="city">{$LANG.clientareacity}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="city" id="city" value="{$contactcity}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="state">{$LANG.clientareastate}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="state" id="state" value="{$contactstate}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="postcode">{$LANG.clientareapostcode}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="postcode" id="postcode" value="{$contactpostcode}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="country">{$LANG.clientareacountry}</label>
		<div class="col-sm-9">
		    {$countriesdropdown}
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="phonenumber">{$LANG.clientareaphonenumber}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-3" name="phonenumber" id="phonenumber" value="{$contactphonenumber}" />
		</div>
	</div>

	<div class="hr hr-dotted"></div>
	
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="billingcontact">{$LANG.subaccountactivate}</label>
		<div class="col-sm-9">
			<div class="tcb">
			<label>
				<input type="checkbox" class="tc" name="subaccount" id="subaccount"{if $subaccount} checked{/if} />
				<span class="labels"> {$LANG.subaccountactivatedesc}</span>
			</label>
			</div>
		</div>
	</div>

</fieldset>

<div id="subaccountfields" class="{if !$subaccount} hide{/if}">

<fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password">{$LANG.clientareapassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password" id="password" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password2">{$LANG.clientareaconfirmpassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password2" id="password2" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="passstrength">{$LANG.pwstrength}</label>
		<div class="col-sm-9">
            {include file="$template/pwstrength.tpl"}
		</div>
	</div>
	
	<div class="hr hr-dotted"></div>

    <div class="form-group">
	    <label class="full col-sm-3 control-label">{$LANG.subaccountpermissions}</label>
		<div class="col-sm-9">
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" value="profile"{if in_array('profile',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsprofile}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permcontacts" value="contacts"{if in_array('contacts',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermscontacts}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permproducts" value="products"{if in_array('products',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsproducts}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permmanageproducts" value="manageproducts"{if in_array('manageproducts',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsmanageproducts}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permdomains" value="domains"{if in_array('domains',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsdomains}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permmanagedomains" value="managedomains"{if in_array('managedomains',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsmanagedomains}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="perminvoices" value="invoices"{if in_array('invoices',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsinvoices}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permtickets" value="tickets"{if in_array('tickets',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermstickets}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permaffiliates" value="affiliates"{if in_array('affiliates',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsaffiliates}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permemails" value="emails"{if in_array('emails',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsemails}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="permissions[]" id="permorders" value="orders"{if in_array('orders',$permissions)} checked{/if} />
                        <span class="labels"> {$LANG.subaccountpermsorders}</span>
                    </label>
				</div>
		</div>
	</div>

</fieldset>

</div>

<fieldset>

	<div class="hr hr-dotted"></div>

    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.clientareacontactsemails}</label>
		<div class="col-sm-9">
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="generalemails" id="generalemails" value="1"{if $generalemails} checked{/if} />
                        <span class="labels"> {$LANG.clientareacontactsemailsgeneral}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="productemails" id="productemails" value="1"{if $productemails} checked{/if} />
                        <span class="labels"> {$LANG.clientareacontactsemailsproduct}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="domainemails" id="domainemails" value="1"{if $domainemails} checked{/if} />
                        <span class="labels"> {$LANG.clientareacontactsemailsdomain}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="invoiceemails" id="invoiceemails" value="1"{if $invoiceemails} checked{/if} />
                        <span class="labels"> {$LANG.clientareacontactsemailsinvoice}</span>
                    </label>
				</div>
				<div class="tcb">
                    <label>
                        <input type="checkbox" class="tc" name="supportemails" id="supportemails" value="1"{if $supportemails} checked{/if} />
                        <span class="labels"> {$LANG.clientareacontactsemailssupport}</span>
                    </label>
				</div>
		</div>
	</div>

</fieldset>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
		<input class="btn" type="reset" value="{$LANG.cancel}" />
    <input class="btn" type="button" value="{$LANG.clientareadeletecontact}" onclick="deleteContact()" />
	</div>
</div>

</form>
{else}

{include file="$template/clientareaaddcontact.tpl"}

{/if}