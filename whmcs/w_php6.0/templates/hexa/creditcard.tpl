<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
<script type="text/javascript" src="includes/jscript/creditcard.js"></script>
{include file="$template/pageheader.tpl" title=$LANG.creditcard}
<div class="alert alert-block alert-warning">
  <p class="textcenter"><strong>Paying Invoice #{$invoiceid}</strong> - Balance Due: <strong>{$balance}</strong></p>
</div>
{if $remotecode}
<div id="submitfrm" class="textcenter">
  {$remotecode}
  <iframe name="3dauth" width="90%" height="600" scrolling="auto" src="about:blank" style="border:1px solid #fff;"></iframe>
</div>
{literal}
<script language="javascript">
  setTimeout ( "autoForward()" , 1000 );
  function autoForward() {
   var submitForm = $("#submitfrm").find("form");
   submitForm.submit();
 }
</script>
{/literal}
{else}
<form method="post" action="creditcard.php">
  <input type="hidden" name="action" value="submit">
  <input type="hidden" name="invoiceid" value="{$invoiceid}">
  {if $errormessage}
  <div class="alert alert-danger">
    <p>{$LANG.clientareaerrors}</p>
    <ul>
      {$errormessage}
    </ul>
  </div>
  {/if}
  {include file="$template/subheader.tpl" title=$LANG.creditcardyourinfo}
  <div class="row">
    <div class="col-lg-6">
      <label for="firstname">{$LANG.clientareafirstname}</label>
      <div class="form-group">
        <input type="text" name="firstname" id="firstname" value="{$firstname}"{if in_array('firstname',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
      </div>
      <label for="lastname">{$LANG.clientarealastname}</label>
      <div class="form-group">
        <input type="text" name="lastname" id="lastname" value="{$lastname}"{if in_array('lastname',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
      </div>
      <label for="address1">{$LANG.clientareaaddress1}</label>
      <div class="form-group">
        <input type="text" name="address1" id="address1" value="{$address1}"{if in_array('address1',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
      </div>
      <label for="address2">{$LANG.clientareaaddress2}</label>
      <div class="form-group">
        <input type="text" name="address2" id="address2" value="{$address2}"{if in_array('address2',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
      </div>
      <label for="city">{$LANG.clientareacity}</label>
      <div class="form-group">
        <input type="text" name="city" id="city" value="{$city}"{if in_array('city',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
      </div>
    </div>
    <div class="col-lg-6">
     <label for="state">{$LANG.clientareastate}</label>
     <div class="form-group">
      <input type="text" name="state" id="state" value="{$state}"{if in_array('state',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
    </div>
    <label for="postcode">{$LANG.clientareapostcode}</label>
    <div class="form-group">
      <input type="text" name="postcode" id="postcode" value="{$postcode}"{if in_array('postcode',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
    </div>
    <label for="country">{$LANG.clientareacountry}</label>
    <div class="form-group">
      {$countriesdropdown|replace:'name="country"':'name="country" style="width:100%; height: 34px; padding: 6px 12px; font-size: 14px; border-radius: 4px; vertical-align: middle; border: 1px solid #ccc; color: #555; line-height: 1.428571429;"'}
    </div>
    <label for="phonenumber">{$LANG.clientareaphonenumber}</label>
    <div class="form-group">
      <input type="text" name="phonenumber" id="phonenumber" value="{$phonenumber}"{if in_array('phonenumber',$uneditablefields)} disabled="" class="disabled form-control"{else} class="form-control"{/if} />
    </div>
  </div>
</div>
{include file="$template/subheader.tpl" title=$LANG.creditcarddetails}
<p><small><span class="glyphicon glyphicon-lock"></span> {$LANG.creditcardsecuritynotice}</small></p>
<div class="row">
<div class="col-lg-12">
    <div class="radio"><label><input type="radio" name="ccinfo" value="useexisting" onclick="disableFields('newccinfo',true)"{if $cardnum} checked{else} disabled{/if} /> {$LANG.creditcarduseexisting}{if $cardnum} ({$cardnum}){/if}</label></div>
    {if $cardnum}
    <label for="cccvv2">{$LANG.creditcardcvvnumber}</label>
    <input type="text" name="cccvv2" id="cccvv2" size="5" value="{$cccvv}" autocomplete="off" class="form-control" />&nbsp;<a href="#" onclick="window.open('images/ccv.gif','','width=280,height=200,scrollbars=no,top=100,left=100');return false">{$LANG.creditcardcvvwhere}</a>
    {/if}
    <div class="radio"><label><input type="radio" name="ccinfo" value="new" onclick="disableFields('newccinfo',false)"{if !$cardnum || $ccinfo eq "new"} checked{/if} /> {$LANG.creditcardenternewcard}</label></div>
    <label for="cctype">{$LANG.creditcardcardtype}</label>
    <div class="form-group">
      <select name="cctype" id="cctype" class="form-control">
        {foreach from=$acceptedcctypes item=cardtype}
        <option{if $cctype eq $cardtype} selected{/if}>{$cardtype}</option>
        {/foreach}
      </select>
    </div>
    <label for="ccnumber">{$LANG.creditcardcardnumber}</label>
    <div class="form-group">
     <input type="text" name="ccnumber" id="ccnumber" value="{$ccnumber}" autocomplete="off" class="form-control" />
   </div>
   {if $showccissuestart}
   <label for="ccstartmonth">{$LANG.creditcardcardstart}</label>
   <div class="row"><div class="col-lg-6"><select name="ccstartmonth" id="ccstartmonth" class="form-control">{foreach from=$months item=month}
    <option{if $ccstartmonth eq $month} selected{/if}>{$month}</option>
    {/foreach}</select></div><div class="col-lg-6"><select name="ccstartyear" class="form-control">
    {foreach from=$startyears item=year}
    <option{if $ccstartyear eq $year} selected{/if}>{$year}</option>
    {/foreach}
  </select></div>
</div>
{/if}
<label for="ccexpirymonth">{$LANG.creditcardcardexpires}</label>
<div class="row"><div class="col-lg-6"><select name="ccexpirymonth" class="form-control" id="ccexpirymonth">{foreach from=$months item=month}
  <option{if $ccexpirymonth eq $month} selected{/if}>{$month}</option>
  {/foreach}</select></div><div class="col-lg-6"><select name="ccexpiryyear" class="form-control">
  {foreach from=$expiryyears item=year}
  <option{if $ccexpiryyear eq $year} selected{/if}>{$year}</option>
  {/foreach}
</select></div>
</div>
{if $showccissuestart}
<label for="ccissuenum">{$LANG.creditcardcardissuenum}</label>
<div class="form-group"><input type="text" class="form-control" name="ccissuenum" id="ccissuenum" maxlength="3" value="{$ccissuenum}" />
</div>
{/if}
<label for="cccvv">{$LANG.creditcardcvvnumber}</label>
<div class="form-group"><input type="text" name="cccvv" id="cccvv" class="form-control" value="{$cccvv}" autocomplete="off" />&nbsp;<a href="#" onclick="window.open('images/ccv.gif','','width=280,height=200,scrollbars=no,top=100,left=100');return false">{$LANG.creditcardcvvwhere}</a></div>
{if $shownostore}
<div class="checkbox"><label><input type="checkbox" name="nostore" id="nostore" class="newccinfo" /> {$LANG.creditcardnostore}</label></div>
{/if}
</div>
</div>
<p><input class="btn btn-primary" type="submit" value="{$LANG.ordercontinuebutton}" onclick="this.value='{$LANG.pleasewait}'" /></p>
{if !$cardnum || $ccinfo eq "new"}{else}
<script> disableFields('newccinfo',true); </script>
{/if}
</form>
{/if}
