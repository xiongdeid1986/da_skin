{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.twofactorauth}

{if $newbackupcode}
<div class="alert alert-success">
    <p>{$LANG.twofabackupcodereset}</p>
</div>
{elseif $incorrect}
<div class="alert alert-danger">
    <p>{$LANG.twofa2ndfactorincorrect}</p>
</div>
{elseif $error}
<div class="alert alert-danger">
    <p>{$error}</p>
</div>
{else}
<div class="alert alert-warning">
    <p>{$LANG.twofa2ndfactorreq}</p>
</div>
{/if}

<form method="post" action="{$systemsslurl}dologin.php" class="form-stacked" id="frmlogin">

{if $newbackupcode}

<input type="hidden" name="newbackupcode" value="1" />
<h2>{$LANG.twofanewbackupcodeis}</h2>
<div class="alert alert-warning twofabackupcode">
    <p>{$newbackupcode}</p>
</div>
<p>{$LANG.twofabackupcodeexpl}</p>
<br />
<p><input type="submit" value="{$LANG.continue} &raquo;" class="btn" /></p>

{elseif $backupcode}

<br />

<input type="hidden" name="backupcode" value="1" />
<p align="center"><input type="text" name="code" size="25" /> <input type="submit" value="{$LANG.loginbutton} &raquo;" class="btn btn-primary" /></p>
<p align="center"></p>

{else}

<br />

{$challenge}

{/if}

<br />

{if !$newbackupcode}
<div class="alert alert-info">
{if $backupcode}
{$LANG.twofabackupcodelogin}
{else}
{$LANG.twofacantaccess2ndfactor} <a href="clientarea.php?backupcode=1">{$LANG.twofaloginusingbackupcode}</a></p>
{/if}
</div>
{/if}

</form>

<script type="text/javascript">
$("#frmlogin input:text:visible:first").focus();
</script>

<br /><br /><br /><br />