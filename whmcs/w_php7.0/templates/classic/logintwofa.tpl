<div class="halfwidthcontainer">

{include file="$template/pageheader.tpl" title=$LANG.twofactorauth}

{if $newbackupcode}
<div class="successbox">
    {$LANG.twofabackupcodereset}
</div>
{elseif $incorrect}
<div class="errorbox">
    {$LANG.twofa2ndfactorincorrect}
</div>
{elseif $error}
<div class="errorbox">
    {$error}
</div>
{else}
<div class="successbox">
    {$LANG.twofa2ndfactorreq}
</div>
{/if}

<form method="post" action="{$systemsslurl}dologin.php" class="form-stacked" name="frmlogin">

    <br />
    {if $newbackupcode}

        <input type="hidden" name="newbackupcode" value="1" />
        <h2 align="center">{$LANG.twofanewbackupcodeis}</h2>
        <div class="alert alert-warning textcenter twofabackupcode">
            <p>{$newbackupcode}</p>
        </div>
        <p align="center">{$LANG.twofabackupcodeexpl}</p>
        <br />
        <p align="center"><input type="submit" value="{$LANG.continue} &raquo;" class="button" /></p>

    {elseif $backupcode}

        <br />

        <input type="hidden" name="backupcode" value="1" />
        <p align="center"><input type="text" name="code" size="25" /> <input type="submit" value="{$LANG.loginbutton}" class="button" /></p>
        <p align="center"></p>

    {else}
        {$challenge}
    {/if}
    <br />

    {if !$newbackupcode}
        <div class="alert alert-block alert-info textcenter">
            {if $backupcode}
                {$LANG.twofabackupcodelogin}
            {else}
                {$LANG.twofacantaccess2ndfactor} <a href="clientarea.php?backupcode=1">{$LANG.twofaloginusingbackupcode}</a></p>
            {/if}
        </div>
    {/if}

</form>

<br /><br /><br /><br />

</div>