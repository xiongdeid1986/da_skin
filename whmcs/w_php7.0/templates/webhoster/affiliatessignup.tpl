{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{if $affiliatesystemenabled}
{include file="$template/pageheader.tpl" title=$LANG.affiliatesactivate}
<div class="note note-info">
	<h2>{$LANG.affiliatesignuptitle}</h2>
	<p>{$LANG.affiliatesintrotext}</p>
</div>

<ul class="list-unstyled">
  <li><i class="fa fa-check bigger-110"></i> {$LANG.affiliatesbullet1} {$bonusdeposit}</li>
  <li><i class="fa fa-check bigger-110"></i> {$LANG.affiliatesearn} <span class="text-primary">{$payoutpercentage}</span> {$LANG.affiliatesbullet2}</li>
</ul>
<p>{$LANG.affiliatesfootertext}</p>
<br />
<form method="post" action="affiliates.php">
<input type="hidden" name="activate" value="true" />
<p><input type="submit" class="btn btn-success" value="{$LANG.affiliatesactivate}" /></p>
</form>
{else}
<p>{$LANG.affiliatesdisabled}</p>
{/if}<br />
