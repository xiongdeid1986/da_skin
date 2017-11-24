<script type="text/javascript" src="includes/jscript/creditcard.js"></script>

{include file="$template/clientareadetailslinks.tpl"}

<p class="heading2">{$LANG.clientareanavchangecc}</p>

{if $remoteupdatecode}

  <div align="center">
    {$remoteupdatecode}
  </div>

{else}

{if $successful}<div class="successbox">{$LANG.changessavedsuccessfully}</div><br />{/if}
{if $errormessage}<div class="errorbox">{$errormessage|replace:'<li>':' &nbsp;#&nbsp; '} &nbsp;#&nbsp; </div><br />{/if}

<table cellspacing="1" cellpadding="0" class="frame"><tr><td>
<table width="100%" cellpadding="2">
<tr><td width="150" class="fieldarea">{$LANG.creditcardcardtype}</td><td>{$cardtype}</td></tr>
<tr><td class="fieldarea">{$LANG.creditcardcardnumber}</td><td>{$cardnum}</td>{if $allowcustomerdelete && $cardtype}<td align="center"><form method="post" action="{$smarty.server.PHP_SELF}?action=creditcard"><input type="submit" name="remove" value="{$LANG.creditcarddelete}" class="button" /></form></td>{/if}</tr>
<tr><td class="fieldarea">{$LANG.creditcardcardexpires}</td><td>{$cardexp}</td></tr>
{if $cardstart}<tr><td class="fieldarea">{$LANG.creditcardcardstart}</td><td>{$cardstart}</td></tr>{/if}
{if $cardissuenum}<tr><td class="fieldarea">{$LANG.creditcardcardissuenum}</td><td>{$cardissuenum}</td></tr>{/if}
</table>
</td></tr></table>

<p>{$LANG.creditcardenternewcard}</p>

<form method="post" action="{$smarty.server.PHP_SELF}?action=creditcard">
<input type="hidden" name="submit" value="true" />

<table cellspacing="1" cellpadding="0" class="frame"><tr><td>
<table width="100%" cellpadding="2">
<tr><td width="150" class="fieldarea">{$LANG.creditcardcardtype}</td><td><select name="cctype" id="cctype">
{foreach key=num item=cardtype from=$acceptedcctypes}
<option{if $cardtype eq $cctype} selected{/if}>{$cardtype}</option>
{/foreach}
</select></td></tr>
<tr><td class="fieldarea">{$LANG.creditcardcardnumber}</td><td><input type="text" name="ccnumber" size="25" value="{$ccnumber}" autocomplete="off" /></td></tr>
<tr><td class="fieldarea">{$LANG.creditcardcardexpires}</td><td><select name="ccexpirymonth" id="ccexpirymonth">{foreach from=$months item=month}
<option{if $ccexpirymonth eq $month} selected{/if}>{$month}</option>
{/foreach}</select> / <select name="ccexpiryyear">
{foreach from=$expiryyears item=year}
<option{if $ccexpiryyear eq $year} selected{/if}>{$year}</option>
{/foreach}</select></td></tr>
{if $showccissuestart}
<tr><td class="fieldarea">{$LANG.creditcardcardstart}</td><td><select name="ccstartmonth" id="ccstartmonth">{foreach from=$months item=month}
<option{if $ccstartmonth eq $month} selected{/if}>{$month}</option>
{/foreach}</select> / <select name="ccstartyear">
{foreach from=$startyears item=year}
<option{if $ccstartyear eq $year} selected{/if}>{$year}</option>
{/foreach}</select></td></tr>
<tr><td class="fieldarea">{$LANG.creditcardcardissuenum}</td><td><input type="text" name="ccissuenum" value="{$ccissuenum}" size="5" maxlength="3" /></td></tr>
{/if}
<tr><td class="fieldarea">{$LANG.creditcardcvvnumber}</td><td><input type="text" name="cardcvv" id="cardcvv" value="{$cardcvv}" size="5" maxlength="3" /></td></tr>
</table>
</td></tr></table>

<p align="center"><input type="submit" value="{$LANG.clientareasavechanges}" class="buttongo" /> <input type="reset" value="{$LANG.clientareacancel}" class="button" /></p>

</form>

{/if}
