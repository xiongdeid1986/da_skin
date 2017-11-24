{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<script type="text/javascript" src="includes/jscript/statesdropdown.js"></script>
{include file="$template/pageheader.tpl" title=$LANG.clientregistertitle desc=$LANG.registerintro}

{if $noregistration}

    <div class="alert alert-danger text-center">
        <p>{$LANG.registerdisablednotice}</p>
    </div>

{else}

{if $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}">
<input type="hidden" name="register" value="true" />

<fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="email">{$LANG.clientareaemail}</label>
		<div class="col-sm-9">
		    <input type="text" class="col-xs-12 col-sm-5" name="email" id="email" value="{$clientemail}"{if in_array('email',$uneditablefields)} disabled="" class="disabled"{/if} />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password">{$LANG.clientareapassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password" id="password" value="{$clientpassword}" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="password2">{$LANG.clientareaconfirmpassword}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="password2" id="password2" value="{$clientpassword2}" />
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

	<div class="hr hr-dotted"></div>
		
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

</fieldset>

<fieldset>

{if $currencies}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="currency">{$LANG.choosecurrency}</label>
		<div class="col-sm-9" id="currency">
		    <select class="input-small" name="currency">
            {foreach from=$currencies item=curr}
            <option value="{$curr.id}"{if !$smarty.post.currency && $curr.default || $smarty.post.currency eq $curr.id } selected{/if}>{$curr.code}</option>
            {/foreach}
            </select>
		</div>
	</div>
{/if}
	
{foreach key=num item=customfield from=$customfields}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="customfield{$customfield.id}">{$customfield.name}</label>
		<div class="col-sm-9">
		    {$customfield.input} {$customfield.description}
		</div>
	</div>
{/foreach}

{if $securityquestions}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="securityqans">{$LANG.clientareasecurityquestion}</label>
		<div class="col-sm-9">
		    <select name="securityqid" id="securityqid">
            {foreach key=num item=question from=$securityquestions}
            	<option value={$question.id}>{$question.question}</option>
            {/foreach}
            </select>
		</div>
	</div>
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="securityqans">{$LANG.clientareasecurityanswer}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="password" name="securityqans" id="securityqans" />
		</div>
	</div>
{/if}

</fieldset>

		{if $capatacha}
		<div class="hr hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label">{$LANG.captchatitle}</label>
				<div class="col-xs-12 col-sm-6">
					<p>{$LANG.captchaverify}</p>
				{if $capatacha eq "recaptcha"}
					<div align="center">{$recapatchahtml}</div>
				{else}
				<p><img src="includes/verifyimage.php" align="middle" /> <input type="text" name="code" size="10" maxlength="5" class="input-small" /></p>
				{/if}
				</div>
		</div>
		{/if}
<br />

{if $accepttos}
<div class="form-group">
    <label id="tosagree"></label>
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
		<div class="tcb">
			<label>
				<input type="checkbox" class="tc" name="accepttos" id="accepttos" value="on" >
				<span class="labels"> {$LANG.ordertosagreement} <a href="{$tosurl}" target="_blank">{$LANG.ordertos}</a></span>
			</label>
		</div>
    </div>
</div>
{/if}

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" value="{$LANG.clientregistertitle}" />
	</div>
</div>

</form>
{/if}

<br />
<br />