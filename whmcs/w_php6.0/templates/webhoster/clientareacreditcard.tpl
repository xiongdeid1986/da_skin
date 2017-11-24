{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<script type="text/javascript" src="includes/jscript/creditcard.js"></script>

{include file="$template/pageheader.tpl" title=$LANG.clientareanavccdetails}
{include file="$template/clientareadetailslinks.tpl"}

{if $remoteupdatecode}

  <div align="center">
    {$remoteupdatecode}
  </div>

{else}

{if $successful}
<div class="alert alert-success">
    <p>{if $deletecc}{$LANG.creditcarddeleteconfirmation}{else}{$LANG.changessavedsuccessfully}{/if}</p>
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
<form class="form-horizontal" method="post" action="{$smarty.server.PHP_SELF}?action=creditcard">

  <fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcardtype}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="text" value="{$cardtype}" disabled="true" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcardnumber}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-5" type="text" value="{$cardnum}" disabled="true" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcardexpires}</label>
		<div class="col-sm-9">
		    <input style="width: 94px;" type="text" value="{$cardexp}" disabled="true" class="input-small" />
		</div>
	</div>
{if $cardissuenum}
    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcardissuenum}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="text" value="{$cardissuenum}" disabled="true" class="input-small" />
		</div>
	</div>
{/if}{if $cardstart}
    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcardstart}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-3" type="text" value="{$cardstart}" disabled="true" class="input-mini" />
		</div>
	</div>
{/if}
{if $allowcustomerdelete && $cardtype}
    <div class="form-group">
	    <label class="col-sm-3 control-label">&nbsp;</label>
		<div class="col-sm-9">
            <form method="post" action="{$smarty.server.PHP_SELF}?action=creditcard">
				<input type="submit" name="remove" value="{$LANG.creditcarddelete}" class="btn btn-danger" />
			</form>            
        </div>
    </div>
{/if}
  </fieldset>

<h4>{$LANG.creditcardenternewcard}</h4>
<div class="hr hr16 hr-dotted"></div>

  <br />

  <fieldset>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="cctype">{$LANG.creditcardcardtype}</label>
		<div class="col-sm-9">
		    <select class="col-xs-12 col-sm-3" name="cctype" id="cctype">
            {foreach key=num item=cardtype from=$acceptedcctypes}
                <option>{$cardtype}</option>
            {/foreach}
            </select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="ccnumber">{$LANG.creditcardcardnumber}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-5" type="text" name="ccnumber" id="ccnumber" autocomplete="off" />
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="ccexpirymonth">{$LANG.creditcardcardexpires}</label>
		<div class="col-sm-9">
		    <select class="col-xs-12" style="width: 64px;" name="ccexpirymonth" id="ccexpirymonth">{foreach from=$months item=month}<option>{$month}</option>{/foreach}</select> / <select style="width: 74px;" name="ccexpiryyear">{foreach from=$expiryyears item=year}<option>{$year}</option>{/foreach}</select>
		</div>
	</div>
{if $showccissuestart}
    <div class="form-group">
	    <label class="col-sm-3 control-label" for="ccstartmonth">{$LANG.creditcardcardstart}</label>
		<div class="col-sm-9">
		    <select class="col-xs-12" style="width: 64px;" name="ccstartmonth" id="ccstartmonth">{foreach from=$months item=month}<option>{$month}</option>{/foreach}</select> / <select style="width: 74px;" name="ccstartyear">{foreach from=$startyears item=year}<option>{$year}</option>{/foreach}</select>
		</div>
	</div>

    <div class="form-group">
	    <label class="col-sm-3 control-label" for="ccissuenum">{$LANG.creditcardcardissuenum}</label>
		<div class="col-sm-9">
		    <input class="col-xs-12 col-sm-2" type="text" name="ccissuenum" id="ccissuenum" maxlength="3" class="input-small" autocomplete="off"/>
		</div>
	</div>
{/if}

    <div class="form-group">
	    <label class="col-sm-3 control-label">{$LANG.creditcardcvvnumber}</label>
		<div class="col-sm-9">
		    <input class="input-small" style="width: 94px;" maxlength="3" type="text" name="cardcvv" id="cardcvv" value="{$cardcvv}" autocomplete="off" />
		</div>
	</div>


  </fieldset>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
		<input class="btn" type="reset" value="{$LANG.cancel}" />
	</div>
</div>

</form>

{/if}

<br /></br /><br />