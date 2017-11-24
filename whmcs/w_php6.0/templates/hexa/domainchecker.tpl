{include file="$template/pageheader.tpl" title=$LANG.domaintitle desc=$LANG.domaincheckerintro}

{if $inccode}
<div class="alert alert-danger">
    {$LANG.captchaverifyincorrect}
</div>
{/if}

{if $bulkdomainsearchenabled}<div class="row"><div class="col-lg-12"><p class="pull-right"><a href="domainchecker.php?search=bulkregister">{$LANG.domainbulksearch}</a> | <a href="domainchecker.php?search=bulktransfer">{$LANG.domainbulktransfersearch}</a></p></div></div>{/if}

<div class="panel panel-default">
  <div class="panel-heading">
    {$LANG.domaincheckerenterdomain}</div>
    <div class="panel-body">
    <form method="post" action="domainchecker.php">
        <div class="form-group"><input type="button" value="{$LANG.searchmultipletlds} &raquo;" class="btn btn-default btn-sm" onclick="jQuery('#tlds').slideToggle()" /></div>
        <div class="form-group"><input class="form-control" name="domain" type="text" value="{if $domain}{$domain}{else}{$LANG.domaincheckerdomainexample}{/if}" onfocus="if(this.value=='{$LANG.domaincheckerdomainexample}')this.value=''" onblur="if(this.value=='')this.value='{$LANG.domaincheckerdomainexample}'" /></div>
    <div class="domcheckertldselect well subhide" id="tlds">
        {foreach from=$tldslist key=num item=listtld}
            <div class="col-lg-2"><label><input type="checkbox" name="tlds[]" value="{$listtld}"{if in_array($listtld,$tlds) || !$tlds && $num==1} checked{/if}> {$listtld}</label></div>
        {/foreach}
        <div class="clear"></div>
    </div>
        {if $capatacha}
        <div class="captchainput">
            <p>{$LANG.captchaverify}</p>
            {if $capatacha eq "recaptcha"}
            <p>{$recapatchahtml}</p>
            {else}
            <p><img src="includes/verifyimage.php" alt="Verify Image" /> <input type="text" name="code" class="input-small" maxlength="5" /></p>
            {/if}
        </div>
        {/if}
        <p><input type="submit" value="{$LANG.checkavailability}" class="btn btn-primary" />{if $condlinks.domaintrans} <input type="submit" name="transfer" value="{$LANG.domainstransfer}" class="btn btn-success" />{/if} <input type="submit" name="hosting" value="{$LANG.domaincheckerhostingonly}" class="btn btn-default" /></p>
</form>
</div>
</div>

{if $lookup}

{if $invalidtld}
    <p class="fontsize3 domcheckererror textcenter">{$invalidtld|strtoupper} {$LANG.domaincheckerinvalidtld}</p>
{elseif $available}
    <p class="fontsize3 domcheckersuccess textcenter">{$LANG.domainavailable1} <strong>{$sld}{$ext}</strong> {$LANG.domainavailable2}</p>
{elseif $invalidtld}
    <p class="fontsize3 domcheckererror textcenter">{$invalidtld|strtoupper} {$LANG.domaincheckerinvalidtld}</p>
{elseif $invalid}
    <p class="fontsize3 domcheckererror textcenter">{$LANG.ordererrordomaininvalid}</p>
{elseif $error}
    <p class="fontsize3 domcheckererror textcenter">{$LANG.domainerror}</p>
{else}
    <p class="fontsize3 domcheckererror textcenter">{$LANG.domainunavailable1} <strong>{$sld}{$ext}</strong> {$LANG.domainunavailable2}</p>
{/if}

{if !$invalid}

<form method="post" action="{$systemsslurl}cart.php?a=add&domain=register">

<table class="table table-striped table-responsive">
    <thead>
        <tr>
            <th></th>
            <th>{$LANG.domainname}</th>
            <th class="textcenter">{$LANG.domainstatus}</th>
            <th class="textcenter">{$LANG.domainmoreinfo}</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$availabilityresults key=num item=result}
        <tr>
            <td>{if $result.status eq "available"}<input type="checkbox" name="domains[]" value="{$result.domain}" {if $num eq "0" && $available}checked {/if}/><input type="hidden" name="domainsregperiod[{$result.domain}]" value="{$result.period}" />{else}X{/if}</td>
            <td>{$result.domain}</td>
            <td class="{if $result.status eq "available"}success{else}danger{/if}">{if $result.status eq "available"}{$LANG.domainavailable}{else}{$LANG.domainunavailable}{/if}</td>
            <td class="textcenter">{if $result.status eq "unavailable"}<a href="http://{$result.domain}" target="_blank">WWW</a> <a href="#" onclick="popupWindow('whois.php?domain={$result.domain}','whois',650,420);return false">WHOIS</a>{else}<select class="form-control" name="domainsregperiod[{$result.domain}]">{foreach key=period item=regoption from=$result.regoptions}<option value="{$period}">{$period} {$LANG.orderyears} @ {$regoption.register}</option>{/foreach}</select>{/if}</td>
        </tr>
{/foreach}
</table>

<div class="row">
<div class="col-lg-12">
<p><input type="submit" value="{$LANG.ordernowbutton} &raquo;" class="btn btn-success pull-right" /></p>
</div>
</div>
</form>

{/if}

{else}

{include file="$template/subheader.tpl" title=$LANG.domainspricing}
<table class="table table-striped table-responsive">
    <thead>
        <tr>
            <th class="textcenter">{$LANG.domaintld}</th>
            <th class="textcenter">{$LANG.domainminyears}</th>
            <th class="textcenter">{$LANG.domainsregister}</th>
            <th class="textcenter">{$LANG.domainstransfer}</th>
            <th class="textcenter">{$LANG.domainsrenew}</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$tldpricelist item=tldpricelist}
        <tr>
            <td>{$tldpricelist.tld}</td>
            <td class="textcenter">{$tldpricelist.period}</td>
            <td class="textcenter">{if $tldpricelist.register}{$tldpricelist.register}{else}{$LANG.domainregnotavailable}{/if}</td>
            <td class="textcenter">{if $tldpricelist.transfer}{$tldpricelist.transfer}{else}{$LANG.domainregnotavailable}{/if}</td>
            <td class="textcenter">{if $tldpricelist.renew}{$tldpricelist.renew}{else}{$LANG.domainregnotavailable}{/if}</td>
        </tr>
{/foreach}
    </tbody>
</table>


{if !$loggedin && $currencies}
<form method="post" action="domainchecker.php">
<p class="pull-right">{$LANG.choosecurrency}: <select name="currency" onchange="submit()">{foreach from=$currencies item=curr}
<option value="{$curr.id}"{if $curr.id eq $currency.id} selected{/if}>{$curr.code}</option>
{/foreach}</select> <input type="submit" value="{$LANG.go}" /></p>
</form>
{/if}
{/if}
