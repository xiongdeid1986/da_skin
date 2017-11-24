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
        window.location='clientarea.php?action=contacts&delete=true&id={$contactid}';
    {rdelim}{rdelim}
</script>

<form method="post" class="form-inline" action="{$smarty.server.PHP_SELF}?action=contacts">
    <div class="panel panel-default" style="margin-top:15px;">
      <div class="panel-body">
        {$LANG.clientareachoosecontact}: <div class="form-group"><select name="contactid" class="form-control" onchange="submit()">
        {foreach item=contact from=$contacts}
        <option value="{$contact.id}">{$contact.name} - {$contact.email}</option>
        {/foreach}
        <option value="new" selected="selected">{$LANG.clientareanavaddcontact}</option>
    </select></div><input class="btn btn-default btn-sm" type="submit"  value="{$LANG.go}" />
</div>
</div>
</form>
<form method="post" action="{$smarty.server.PHP_SELF}?action=addcontact">
    <input type="hidden" name="submit" value="true" />
    <div class="row">
        <div class="col-lg-6">
            <label for="firstname">{$LANG.clientareafirstname}</label>
            <div class="form-group">
              <input type="text" class="form-control" name="firstname" id="firstname" value="{$contactfirstname}" />
          </div>
          <label for="lastname">{$LANG.clientarealastname}</label>
          <div class="form-group">
              <input type="text" class="form-control" name="lastname" id="lastname" value="{$contactlastname}" />
          </div>

          <label for="companyname">{$LANG.clientareacompanyname}</label>
          <div class="form-group">
              <input type="text" class="form-control" name="companyname" id="companyname" value="{$contactcompanyname}" />
          </div>

          <label for="email">{$LANG.clientareaemail}</label>
          <div class="form-group">
              <input type="text" class="form-control" name="email" id="email" value="{$contactemail}" />
          </div>
          <label for="billingcontact">{$LANG.subaccountactivate}</label>
          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="subaccount" id="subaccount"{if $subaccount} checked{/if} /> <small>{$LANG.subaccountactivatedesc}</small>
            </label>
        </div>
    </div>
</div>
<div class="col-lg-6">
   <label for="address1">{$LANG.clientareaaddress1}</label>
   <div class="form-group">
      <input type="text" class="form-control" name="address1" id="address1" value="{$contactaddress1}" />
  </div>

  <label for="address2">{$LANG.clientareaaddress2}</label>
  <div class="form-group">
      <input type="text" class="form-control" name="address2" id="address2" value="{$contactaddress2}" />
  </div>

  <label for="city">{$LANG.clientareacity}</label>
  <div class="form-group">
      <input type="text" class="form-control" name="city" id="city" value="{$contactcity}" />
  </div>

  <label for="state">{$LANG.clientareastate}</label>
  <div class="form-group">
      <input type="text" class="form-control" name="state" id="state" value="{$contactstate}" />
  </div>

  <label for="postcode">{$LANG.clientareapostcode}</label>
  <div class="form-group">
      <input type="text" class="form-control" name="postcode" id="postcode" value="{$contactpostcode}" />
  </div>

  <label for="country">{$LANG.clientareacountry}</label>
  <div class="form-group">
      {$countriesdropdown|replace:'name="country"':'name="country" style="width:100%; height: 34px; padding: 6px 12px; font-size: 14px; border-radius: 4px; vertical-align: middle; border: 1px solid #ccc; color: #555; line-height: 1.428571429;"'}
  </div>

  <label for="phonenumber">{$LANG.clientareaphonenumber}</label>
  <div class="form-group">
      <input type="text" class="form-control" name="phonenumber" id="phonenumber" value="{$contactphonenumber}" />
  </div>
</div>
</div>
<div id="subaccountfields" class="well{if !$subaccount} subhide{/if}">

 <label for="password">{$LANG.clientareapassword}</label>
 <input type="password" class="form-control" name="password" id="password" />

 <label for="password2">{$LANG.clientareaconfirmpassword}</label>
 <input type="password" class="form-control" name="password2" id="password2" />

 {include file="$template/pwstrength.tpl"}
 <p>{$LANG.subaccountpermissions}</p>

 <div class="col-lg-6">    
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" value="profile"{if in_array('profile',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsprofile}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permcontacts" value="contacts"{if in_array('contacts',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermscontacts}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permproducts" value="products"{if in_array('products',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsproducts}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permmanageproducts" value="manageproducts"{if in_array('manageproducts',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsmanageproducts}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permdomains" value="domains"{if in_array('domains',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsdomains}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permmanagedomains" value="managedomains"{if in_array('managedomains',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsmanagedomains}</span>
        </label>
    </div>
</div>
<div class="col-lg-6">

    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="perminvoices" value="invoices"{if in_array('invoices',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsinvoices}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permtickets" value="tickets"{if in_array('tickets',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermstickets}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permaffiliates" value="affiliates"{if in_array('affiliates',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsaffiliates}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permemails" value="emails"{if in_array('emails',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsemails}</span>
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="permissions[]" id="permorders" value="orders"{if in_array('orders',$permissions)} checked{/if} />
            <span>{$LANG.subaccountpermsorders}</span>
        </label>
    </div>
</div>
<div class="clearfix"></div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">{$LANG.clientareacontactsemails}</div>
  <div class="panel-body">
      <div class="col-lg-6">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="generalemails" id="generalemails" value="1"{if $generalemails} checked{/if} />
                <small>{$LANG.clientareacontactsemailsgeneral}</small>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="productemails" id="productemails" value="1"{if $productemails} checked{/if} />
                <small>{$LANG.clientareacontactsemailsproduct}</small>                
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="domainemails" id="domainemails" value="1"{if $domainemails} checked{/if} />
                <small>{$LANG.clientareacontactsemailsdomain}</small>
            </label>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="invoiceemails" id="invoiceemails" value="1"{if $invoiceemails} checked{/if} />
                <small>{$LANG.clientareacontactsemailsinvoice}</small>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="supportemails" id="supportemails" value="1"{if $supportemails} checked{/if} />
                <small>{$LANG.clientareacontactsemailssupport}</small>
            </label>
        </div>
    </div>
</div>
</div>
<div class="btn-toolbar pull-right" role="toolbar">
<input class="btn btn-primary btn-sm pull-right" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
<input class="btn btn-sm btn-link pull-right" type="reset" value="{$LANG.cancel}" />
</div>
</form>