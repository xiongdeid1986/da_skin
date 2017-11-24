{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
<script type="text/javascript" src="includes/jscript/creditcard.js"></script>

{include file="$template/pageheader.tpl" title=$LANG.creditcard}

<div class="alert alert-block alert-warning">
    <p><strong>Paying Invoice #{$invoiceid}</strong> - Balance Due: <strong>{$balance}</strong></p>
</div>

{if $remotecode}

<div id="submitfrm" class="text-center">

{$remotecode}

<iframe name="3dauth" width="90%" height="600" scrolling="auto" src="about:blank" style="border:1px solid #fff;"></iframe>

</div>

<br />

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
<form method="post" action="creditcard.php" class="form-horizontal">
<input type="hidden" name="action" value="submit">
<input type="hidden" name="invoiceid" value="{$invoiceid}">

	{if $errormessage}
    <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
        <p class="bold">{$LANG.clientareaerrors}</p>
        <ul>
            {$errormessage}
        </ul>
    </div>
{/if}
<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4 class="lighter">{$LANG.creditcardyourinfo}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#cc-box1"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="cc-box1" class="panel-collapse collapse in">
	<div class="portlet-body">
		<fieldset>

			<div class="form-group">
        	    <label class="col-sm-3 control-label" for="firstname">{$LANG.clientareafirstname}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-3" type="text" name="firstname" id="firstname" value="{$firstname}"{if in_array('firstname',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>

            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="lastname">{$LANG.clientarealastname}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-3" type="text" name="lastname" id="lastname" value="{$lastname}"{if in_array('lastname',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>
			
			<div class="hr hr-dotted"></div>
			
            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="address1">{$LANG.clientareaaddress1}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-5" type="text" name="address1" id="address1" value="{$address1}"{if in_array('address1',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>

            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="address2">{$LANG.clientareaaddress2}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-5" type="text" name="address2" id="address2" value="{$address2}"{if in_array('address2',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>

            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="city">{$LANG.clientareacity}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-3" type="text" name="city" id="city" value="{$city}"{if in_array('city',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>

            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="state">{$LANG.clientareastate}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-3" type="text" name="state" id="state" value="{$state}"{if in_array('state',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>

            <div class="form-group">
        	    <label class="col-sm-3 control-label" for="postcode">{$LANG.clientareapostcode}</label>
        		<div class="col-sm-9">
        		    <input class="col-xs-12 col-sm-3" type="text" name="postcode" id="postcode" value="{$postcode}"{if in_array('postcode',$uneditablefields)} disabled="" class="disabled"{/if} />
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
        		    <input class="col-xs-12 col-sm-3" type="text" name="phonenumber" id="phonenumber" value="{$phonenumber}"{if in_array('phonenumber',$uneditablefields)} disabled="" class="disabled"{/if} />
        		</div>
        	</div>
	</div>
	</div>
</div>

<div class="hr hr-dotted"></div>
		
<div class="portlet no-border">
	<div class="portlet-heading">
		<div class="portlet-title">
			<h4>{$LANG.creditcarddetails}</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#cc-box2"><i class="fa fa-chevron-down"></i></a>
		</div>
	<div class="clearfix"></div>		
	</div>
	<div id="cc-box2" class="panel-collapse collapse in">
	<div class="portlet-body">
            <p><label class="radio-inline"><input type="radio" class="radio" name="ccinfo" value="useexisting" onclick="disableFields('newccinfo',true)"{if $cardnum} checked{else} disabled{/if} /> {$LANG.creditcarduseexisting}{if $cardnum} ({$cardnum}){/if}</label></p>
{if $cardnum}
            <br />

            <div class="form-group">
                <label class="col-sm-3 control-label" for="cccvv2">{$LANG.creditcardcvvnumber}</label>
        		<div class="col-sm-9"><input type="text" name="cccvv2" id="cccvv2" size="5" value="{$cccvv}" autocomplete="off" class="m-wrap small" />&nbsp;<a href="#" onclick="window.open('images/ccv.gif','','width=280,height=200,scrollbars=no,top=100,left=100');return false">{$LANG.creditcardcvvwhere}</a></div>
        	</div>
{/if}
            <label class="radio-inline"><input type="radio" class="radio" name="ccinfo" value="new" onclick="disableFields('newccinfo',false)"{if !$cardnum || $ccinfo eq "new"} checked{/if} /> {$LANG.creditcardenternewcard}</label>

            <br />
            <br />

            <div class="form-group">
                <label class="col-sm-3 control-label" for="cctype">{$LANG.creditcardcardtype}</label>
                <div class="col-sm-9">
                	<select name="cctype" id="cctype" class="newccinfo m-wrap medium">
                    {foreach from=$acceptedcctypes item=cardtype}
                        <option{if $cctype eq $cardtype} selected{/if}>{$cardtype}</option>
                    {/foreach}
                    </select>
        		</div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" for="ccnumber">{$LANG.creditcardcardnumber}</label>
        		<div class="col-sm-9"><input type="text" name="ccnumber" id="ccnumber" size="30" value="{$ccnumber}" autocomplete="off" class="newccinfo m-wrap large" /></div>
        	</div>

           {if $showccissuestart}
            <div class="form-group">
                <label class="col-sm-3 control-label" for="ccstartmonth">{$LANG.creditcardcardstart}</label>
        		<div class="col-sm-9"><select name="ccstartmonth" id="ccstartmonth" class="newccinfo">{foreach from=$months item=month}
<option{if $ccstartmonth eq $month} selected{/if}>{$month}</option>
{/foreach}</select> / <select name="ccstartyear" class="newccinfo">
{foreach from=$startyears item=year}
<option{if $ccstartyear eq $year} selected{/if}>{$year}</option>
{/foreach}
</select></div>
        	</div>
            {/if}

            <div class="form-group">
                <label class="col-sm-3 control-label" for="ccexpirymonth">{$LANG.creditcardcardexpires}</label>
        		<div class="col-sm-9"><select name="ccexpirymonth" id="ccexpirymonth" class="newccinfo m-wrap" style="width: 54px;">{foreach from=$months item=month}
<option{if $ccexpirymonth eq $month} selected{/if}>{$month}</option>
{/foreach}</select> / <select name="ccexpiryyear" class="newccinfo m-wrap" style="width: 74px;">
{foreach from=$expiryyears item=year}
<option{if $ccexpiryyear eq $year} selected{/if}>{$year}</option>
{/foreach}
</select></div>
        	</div>

           {if $showccissuestart}
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="ccissuenum">{$LANG.creditcardcardissuenum}</label>
        		<div class="col-sm-9"><input type="text" name="ccissuenum" id="ccissuenum" size="5" maxlength="3" value="{$ccissuenum}" class="newccinfo m-wrap small" /></div>
        	</div>
            {/if}

            <div class="form-group">
                <label class="col-sm-3 control-label" for="cccvv">{$LANG.creditcardcvvnumber}</label>
        		<div class="col-sm-9"><input type="text" name="cccvv" id="cccvv" size="5" value="{$cccvv}" autocomplete="off" class="newccinfo m-wrap small" />&nbsp;<a href="#" onclick="window.open('images/ccv.gif','','width=280,height=200,scrollbars=no,top=100,left=100');return false">{$LANG.creditcardcvvwhere}</a></div>
        	</div>

        	{if $shownostore}
            <p><label class="checkbox"><input type="checkbox" name="nostore" id="nostore" class="newccinfo" /> {$LANG.creditcardnostore}</label></p>
        	{/if}

            <br />
            <br />
			
	</div>
	</div>
</div>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-primary" type="submit" value="{$LANG.ordercontinuebutton}" onclick="this.value='{$LANG.pleasewait}'" />
	</div>
</div>


<p><img src="images/padlock.gif" alt="Secure" /> {$LANG.creditcardsecuritynotice}</p>

</fieldset>

{if !$cardnum || $ccinfo eq "new"}{else}
<script> disableFields('newccinfo',true); </script>
{/if}

</form>

{/if}