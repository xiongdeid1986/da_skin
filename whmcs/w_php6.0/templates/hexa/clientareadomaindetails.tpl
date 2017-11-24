{include file="$template/pageheader.tpl" title=$LANG.managing|cat:' '|cat:$domain}
{if $updatesuccess}
<div class="alert alert-success">
    <p>{$LANG.changessavedsuccessfully}</p>
</div>
{elseif $registrarcustombuttonresult=="success"}
<div class="alert alert-success">
    <p>{$LANG.moduleactionsuccess}</p>
</div>
{elseif $error}
<div class="alert alert-danger">
    <p>{$error}</p>
</div>
{elseif $registrarcustombuttonresult}
<div class="alert alert-danger">
    <p><strong>{$LANG.moduleactionfailed}:</strong> {$registrarcustombuttonresult}</p>
</div>
{elseif $lockstatus=="unlocked"}
<div class="alert alert-danger">
<p><strong>{$LANG.domaincurrentlyunlocked}</strong> {$LANG.domaincurrentlyunlockedexp}</p>
</div>
{/if}
<div id="tabs">
    <ul class="nav nav-tabs">
        <li class="active" id="tab1nav"><a href="#tab1">{$LANG.information}</a></li>
        <li id="tab2nav"><a href="#tab2">{$LANG.domainsautorenew}</a></li>
        {if $rawstatus == "active"}<li id="tab3nav"><a href="#tab3">{$LANG.domainnameservers}</a></li>{/if}
        {if $lockstatus}{if $tld neq "co.uk" && $tld neq "org.uk" && $tld neq "ltd.uk" && $tld neq "plc.uk" && $tld neq "me.uk"}<li id="tab4nav"><a href="#tab4">{$LANG.domainregistrarlock}</a></li>{/if}{/if}
        {if $releasedomain}<li id="tab5nav"><a href="#tab5">{$LANG.domainrelease}</a></li>{/if}
        {if $addonscount}<li id="tab6nav"><a href="#tab6">{$LANG.clientareahostingaddons}</a></li>{/if}
        {if $managecontacts || $registerns || $dnsmanagement || $emailforwarding || $getepp}<li class="dropdown"><a data-toggle="dropdown" href="#" class="dropdown-toggle">{$LANG.domainmanagementtools}&nbsp;<b class="caret"></b></a>
        <ul class="dropdown-menu">
            {if $managecontacts}<li><a href="clientarea.php?action=domaincontacts&domainid={$domainid}">{$LANG.domaincontactinfo}</a></li>{/if}
            {if $registerns}<li><a href="clientarea.php?action=domainregisterns&domainid={$domainid}">{$LANG.domainregisterns}</a></li>{/if}
            {if $dnsmanagement}<li><a href="clientarea.php?action=domaindns&domainid={$domainid}">{$LANG.clientareadomainmanagedns}</a></li>{/if}
            {if $emailforwarding}<li><a href="clientarea.php?action=domainemailforwarding&domainid={$domainid}">{$LANG.clientareadomainmanageemailfwds}</a></li>{/if}
            {if $getepp}<li class="divider"></li>
            <li><a href="clientarea.php?action=domaingetepp&domainid={$domainid}">{$LANG.domaingeteppcode}</a></li>{/if}
            {if $registrarcustombuttons}<li class="divider"></li>
            {foreach from=$registrarcustombuttons key=label item=command}
            <li><a href="clientarea.php?action=domaindetails&amp;id={$domainid}&amp;modop=custom&amp;a={$command}">{$label}</a></li>
            {/foreach}{/if}
        </ul>
    </li>{/if}
</ul>
</div>

<div id="tab1" class="tab-content active">
    <div class="row">
        <div class="col-lg-4">
            <h2><span aria-hidden="true" class="icon icon-info"></span> {$LANG.information}</h2>
            <p><small>{$LANG.domaininfoexp}</small></p>
            <p><input type="button" value="{$LANG.backtodomainslist}" class="btn btn-default btn-sm" onclick="window.location='clientarea.php?action=domains'" /></p>
        </div>
        <div class="col-lg-8">
            <div class="panel panel-default">
              <div class="panel-body">
                <dl class="dl-data dl-horizontal">
                    <dt>{$LANG.clientareahostingregdate} :</dt><dd>{$registrationdate}</dd>
                    <dt>{$LANG.firstpaymentamount} :</dt><dd>{$firstpaymentamount}</dd>
                    <dt>{$LANG.recurringamount} :</dt><dd>{$recurringamount}{if $recreatesubscriptionbutton} &nbsp; {$recreatesubscriptionbutton} {/if}</dd>
                    <dt>{$LANG.clientarearegistrationperiod} :</dt><dd>{$registrationperiod} {$LANG.orderyears}{if $renew}&nbsp; | &nbsp;<a href="cart.php?gid=renewals">{$LANG.domainsrenewnow}</a>{/if}</dd>
                    <dt>{$LANG.clientareahostingnextduedate} :</dt><dd>{$nextduedate}</dd>
                    <dt>{$LANG.orderpaymentmethod} :</dt><dd>{$paymentmethod}</dd>
                </dl>
                {if $registrarclientarea}<div class="moduleoutput">{$registrarclientarea|replace:'modulebutton':'btn'}</div>{/if}
            </div>
        </div>
    </div>
</div>
</div>
<div id="tab2" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2><span aria-hidden="true" class="icon icon-reload"></span> {$LANG.domainsautorenew}</h2>
            <p><small>{$LANG.domainrenewexp}</small></p>
        </div>
        <div class="col-lg-8">
          <div class="panel panel-default">
              <div class="panel-body">
                <h4>{$LANG.domainautorenewstatus}:</h4>
                <p>{if $autorenew}{$LANG.domainsautorenewenabled}{else}{$LANG.domainsautorenewdisabled}{/if}</p>
                <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails" class="form-horizontal">
                    <input type="hidden" name="id" value="{$domainid}">
                    {if $autorenew}
                    <input type="hidden" name="autorenew" value="disable">
                    <p><input type="submit" class="btn btn-danger" value="{$LANG.domainsautorenewdisable}" /></p>
                    {else}
                    <input type="hidden" name="autorenew" value="enable">
                    <p><input type="submit" class="btn btn-success" value="{$LANG.domainsautorenewenable}" /></p>
                    {/if}
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<div id="tab3" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2><span aria-hidden="true" class="icon icon-list"></span> {$LANG.domainnameservers}</h2>
            <p><small>{$LANG.domainnsexp}</small></p>
        </div>
        <div class="col-lg-8">
          <div class="panel panel-default">
              <div class="panel-body">
                <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
                    <input type="hidden" name="id" value="{$domainid}" />
                    <input type="hidden" name="sub" value="savens" />
                    <fieldset class="control-group">
                        <div class="form-group">
                        <div class="radio">
                            <label>
                                <input type="radio" name="nschoice" value="default" onclick="disableFields('domnsinputs',true)"{if $defaultns} checked{/if}>{$LANG.nschoicedefault}
                            </label>
                            </div>
                            <div class="radio">
                            <label>
                                <input type="radio" name="nschoice" value="custom" onclick="disableFields('domnsinputs','')"{if !$defaultns} checked{/if}>{$LANG.nschoicecustom}
                            </label>
                        </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ns1">{$LANG.domainnameserver1}</label>
                            <input class=" domnsinputs form-control" id="ns1" name="ns1" type="text" value="{$ns1}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ns2">{$LANG.domainnameserver2}</label>
                            <input class=" domnsinputs form-control" id="ns2" name="ns2" type="text" value="{$ns2}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ns3">{$LANG.domainnameserver3}</label>
                            <input class=" domnsinputs form-control" id="ns3" name="ns3" type="text" value="{$ns3}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ns4">{$LANG.domainnameserver4}</label>
                            <input class=" domnsinputs form-control" id="ns4" name="ns4" type="text" value="{$ns4}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="ns5">{$LANG.domainnameserver5}</label>
                            <input class=" domnsinputs form-control" id="ns5" name="ns5" type="text" value="{$ns5}" />
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">{$LANG.changenameservers}</button>
                    </fieldset>
                </form>
            </div>

        </div>
    </div>
</div>
</div>
<div id="tab4" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
         <h2>{$LANG.domainregistrarlock}</h2>
         <p><small>{$LANG.domainlockingexp}</small></p>
     </div>
     <div class="col-lg-8">
      <div class="panel panel-default">
          <div class="panel-body">
            <h4>{$LANG.domainreglockstatus}:</h4>
            <p><strong>{if $lockstatus=="locked"}{$LANG.domainsautorenewenabled}{else}{$LANG.domainsautorenewdisabled}{/if}</strong></p>
            <hr />
            <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
                <input type="hidden" name="id" value="{$domainid}" />
                <input type="hidden" name="sub" value="savereglock" />
                {if $lockstatus=="locked"}
                <p><input type="submit" class="btn btn-danger" value="{$LANG.domainreglockdisable}" /></p>
                {else}
                <p><input type="submit" class="btn btn-success" name="reglock" value="{$LANG.domainreglockenable}" /></p>
                {/if}
            </form>
        </div>
    </div>
</div>
</div>
</div>
<div id="tab5" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
           <h2>{$LANG.domainrelease}</h2>
           <p><small>{$LANG.domainreleasedescription}</small></p>
       </div>
       <div class="col-lg-8">
          <div class="panel panel-default">
              <div class="panel-body">
                {if $releasedomain}
                <p><strong>&nbsp;&raquo;&nbsp;&nbsp;{$LANG.domainrelease}</strong></p>
                <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindetails">
                    <input type="hidden" name="sub" value="releasedomain">
                    <input type="hidden" name="id" value="{$domainid}">
                    {$LANG.domainreleasetag}: <input type="text" name="transtag" size="20" />
                    <input type="submit" value="{$LANG.domainrelease}" class="buttonwarn" />
                </form>
                {/if}
            </div>
        </div>
    </div>
</div>
</div>
<div id="tab6" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2><span aria-hidden="true" class="icon icon-plus"></span> {$LANG.domainaddons}</h2>
            <p><small>{$LANG.domainaddonsinfo}</small></p>
        </div>
        <div class="col-lg-8">
        {if $addons.idprotection}
          <div class="panel panel-default">
              <div class="panel-body">
                <h4>{$LANG.domainidprotection}</h4>
                <p>{$LANG.domainaddonsidprotectioninfo}</p>
                {if $addonstatus.idprotection}
                <a href="clientarea.php?action=domainaddons&id={$domainid}&disable=idprotect&token={$token}">{$LANG.disable}</a>
                {else}
                <a href="clientarea.php?action=domainaddons&id={$domainid}&buy=idprotect&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.idprotection}</a>
                {/if}
            </div>
        </div>
        {/if}
        {if $addons.dnsmanagement}
        <div class="panel panel-default">
           <div class="panel-body">
            <h4>{$LANG.domainaddonsdnsmanagement}</h4>
            <p>{$LANG.domainaddonsdnsmanagementinfo}</p>
            {if $addonstatus.dnsmanagement}
            <a href="clientarea.php?action=domaindns&domainid={$domainid}">{$LANG.manage}</a> | <a href="clientarea.php?action=domainaddons&id={$domainid}&disable=dnsmanagement&token={$token}">{$LANG.disable}</a>
            {else}
            <a href="clientarea.php?action=domainaddons&id={$domainid}&buy=dnsmanagement&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.dnsmanagement}</a>
            {/if}
        </div>
    </div>
    {/if}
    {if $addons.emailforwarding}
    <div class="panel panel-default">
        <div class="panel-body">
            <h4>{$LANG.domainemailforwarding}</h4>
            <p>{$LANG.domainaddonsemailforwardinginfo}</p>
            {if $addonstatus.emailforwarding}
            <a href="clientarea.php?action=domainemailforwarding&domainid={$domainid}">{$LANG.manage}</a> | <a href="clientarea.php?action=domainaddons&id={$domainid}&disable=emailfwd&token={$token}">{$LANG.disable}</a>
            {else}
            <a href="clientarea.php?action=domainaddons&id={$domainid}&buy=emailfwd&token={$token}">{$LANG.domainaddonsbuynow} {$addonspricing.emailforwarding}</a>
            {/if}
        </div>
    </div>
    {/if}
</div>
</div>
</div>
