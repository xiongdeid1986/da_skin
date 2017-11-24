{include file="$template/pageheader.tpl" title=$LANG.domaintitle desc=$LANG.domaincheckerintro}

{if $inccode}
<div class="alert alert-danger">
    {$LANG.captchaverifyincorrect}
</div>
{/if}

{if $bulkdomainsearchenabled}<div class="row"><div class="col-lg-12"><p class="pull-right"><a href="domainchecker.php">{$LANG.domainsimplesearch}</a> | <a href="domainchecker.php?search=bulkregister">{$LANG.domainbulksearch}</a></p></div></div>{/if}

<div class="panel panel-default">
  <div class="panel-heading">
        {$LANG.domainbulktransferdescription}
    </div><div class="panel-body">
    <form method="post" action="domainchecker.php?search=bulktransfer">
    <div class="form-group">
        <textarea name="bulkdomains" rows="4" class="form-control">{$bulkdomains}</textarea>
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
<p><input type="submit" value="{$LANG.domainstransfer}" class="btn btn-success" /></p>
</form>
</div>
</div>

{if $invalid}
    <p>{$LANG.domaincheckerbulkinvaliddomain}</p>
{/if}

{if $availabilityresults}

<form method="post" action="{$systemsslurl}cart.php?a=add&domain=transfer">

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
            <td>{if $result.status eq "unavailable"}<input type="checkbox" name="domains[]" value="{$result.domain}" {if $num eq "0" && $available}checked {/if}/><input type="hidden" name="domainsregperiod[{$result.domain}]" value="{$result.period}" />{else}X{/if}</td>
            <td>{$result.domain}</td>
            <td class="{if $result.status eq "unavailable"}success{else}danger{/if}">{if $result.status eq "unavailable"}{$LANG.domaincheckeravailtransfer}{else}{$LANG.domainunavailable}{/if}</td>
            <td>{if $result.status eq "unavailable"}<select class="form-control" name="domainsregperiod[{$result.domain}]">{foreach key=period item=regoption from=$result.regoptions}{if $regoption.transfer}<option value="{$period}">{$period} {$LANG.orderyears} @ {$regoption.transfer}</option>{/if}{/foreach}</select>{/if}</td>
        </tr>
{/foreach}
</table>
<div class="row">
<div class="col-lg-12">
<p><input type="submit" value="{$LANG.ordernowbutton} &raquo;" class="btn btn-danger pull-right" /></p>
</div>
</div>
</form>

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
<div class="info-aa">
  <a href="http://cmsbased.net">cmsbased.net</a>
</div>

{if !$loggedin && $currencies}
<form method="post" action="domainchecker.php">
<p class="pull-right">{$LANG.choosecurrency}: <select name="currency" onchange="submit()">{foreach from=$currencies item=curr}
<option value="{$curr.id}"{if $curr.id eq $currency.id} selected{/if}>{$curr.code}</option>
{/foreach}</select> <input type="submit" value="{$LANG.go}" /></p>
</form>
{/if}
{/if}