<script type="text/javascript" src="includes/jscript/creditcard.js"></script>

{include file="$template/pageheader.tpl" title=$LANG.clientareanavccdetails}
{include file="$template/clientareadetailslinks.tpl"}
{if $remoteupdatecode}
<div align="center">
    {$remoteupdatecode}
</div>
{else}
{if $successful}
<div class="alert alert-success" style="margin-top:15px;">
    <p>{if $deletecc}{$LANG.creditcarddeleteconfirmation}{else}{$LANG.changessavedsuccessfully}{/if}</p>
</div>
{/if}
{if $errormessage}
<div class="alert alert-danger" style="margin-top:15px;">
    <p><i class="fa fa-exclamation-triangle"></i> {$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
        <label class="control-label">{$LANG.creditcardcardtype}</label>
        <input type="text" value="{$cardtype}" disabled="true" class="form-control" />
    </div>
    <div class="form-group">
        <label class="control-label">{$LANG.creditcardcardnumber}</label>
        <input class="form-control" type="text" value="{$cardnum}" disabled="true" />
    </div>
    <div class="form-group">
        <label class="control-label">{$LANG.creditcardcardexpires}</label>
        <input  type="text" value="{$cardexp}" disabled="true" class="form-control" />
    </div>
    {if $cardstart}
    <div class="form-group">
        <label class="control-label">{$LANG.creditcardcardstart}</label>
        <input type="text" value="{$cardstart}" disabled="true" class="form-control" />
    </div>
    {/if}{if $cardissuenum}
    <div class="form-group">
        <label class="control-label">{$LANG.creditcardcardissuenum}</label>
        <input type="text" value="{$cardissuenum}" disabled="true" class="form-control" />
    </div>
    {/if}
    {if $allowcustomerdelete && $cardtype}
    <form method="post" action="{$smarty.server.PHP_SELF}?action=creditcard">
        <input type="submit" name="remove" value="{$LANG.creditcarddelete}" class="btn btn-xs btn-danger pull-right" />
    </form>
    {/if}
</div>
<form method="post" action="{$smarty.server.PHP_SELF}?action=creditcard">
      <div class="col-md-6">
        <div class="form-group">
            <label class="control-label" for="cctype">{$LANG.creditcardcardtype}</label>
            <select class="form-control" name="cctype" id="cctype">
                {foreach key=num item=cardtype from=$acceptedcctypes}
                <option>{$cardtype}</option>
                {/foreach}
            </select>
        </div>
        <div class="row">
           <div class="col-lg-8">
            <div class="form-group">
                <label class="control-label" for="ccnumber">{$LANG.creditcardcardnumber}</label>
                <input class="form-control" type="text" name="ccnumber" id="ccnumber" autocomplete="off" />
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">{$LANG.creditcardcvvnumber}</label>
                <input type="text" name="cardcvv" id="cardcvv" value="{$cardcvv}" class="form-control" autocomplete="off" />
            </div></div></div>
            <label class="control-label" for="ccexpirymonth">{$LANG.creditcardcardexpires}</label> 
            <div class="row">
               <div class="col-lg-6">
                <div class="form-group"> 
                    <select class="form-control" name="ccexpirymonth" id="ccexpirymonth">{foreach from=$months item=month}<option>{$month}</option>{/foreach}</select>
                </div></div>
                <div class="col-lg-6">
                   <div class="form-group">
                    <select class="form-control" name="ccexpiryyear">{foreach from=$expiryyears item=year}<option>{$year}</option>{/foreach}</select>
                </div></div></div>
                {if $showccissuestart}
                <div class="form-group">
                    <label class="control-label" for="ccstartmonth">{$LANG.creditcardcardstart}</label>
                    <select class="form-control" name="ccstartmonth" id="ccstartmonth">{foreach from=$months item=month}<option>{$month}</option>{/foreach}</select> / <select class="form-control" name="ccstartyear">{foreach from=$startyears item=year}<option>{$year}</option>{/foreach}</select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="ccissuenum">{$LANG.creditcardcardissuenum}</label>
                    <input type="text" name="ccissuenum" id="ccissuenum" class="form-control" autocomplete="off" />
                </div>
                {/if}
                <div class="btn-toolbar" role="toolbar">
                    <input class="btn btn-primary pull-right" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
                    <input class="btn btn-link pull-right" type="reset" value="{$LANG.cancel}" />
                </div>
            </div>
        </div>
    </form>
    {/if}