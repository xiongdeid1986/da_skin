{include file="$template/pageheader.tpl" title=$product}

{if $modulechangepwresult eq "success"}
<div class="alert alert-success">
    <p>{$LANG.serverchangepasswordsuccessful}</p>
</div>
{elseif $modulechangepwresult eq "error"}
<div class="alert alert-danger">
    <p>{$modulechangepasswordmessage}</p>
</div>
{elseif $modulecustombuttonresult=="success"}
<div class="alert alert-success">
    <p>{$LANG.moduleactionsuccess}</p>
</div>
{elseif $modulecustombuttonresult}
<div class="alert alert-danger">
    <p><strong>{$LANG.moduleactionfailed}:</strong> {$modulecustombuttonresult}</p>
</div>
{/if}

<div id="tabs">
    <ul class="nav nav-tabs" data-tabs="tabs">
        <li id="tab1nav" class="active"><a href="#tab1" data-toggle="tab">{$LANG.information}</a></li>
        {if $modulechangepassword}<li id="tab2nav"><a href="#tab2" data-toggle="tab">{$LANG.serverchangepassword}</a></li>{/if}
        {if $downloads}<li id="tab3nav"><a href="#tab3" data-toggle="tab">{$LANG.downloadstitle}</a></li>{/if}
        <li id="tab4nav"><a href="#tab4" data-toggle="tab">{$LANG.clientareahostingaddons}</a></li>
        {if $packagesupgrade || $configoptionsupgrade || $showcancelbutton || $modulecustombuttons}<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">{$LANG.productmanagementactions}</a>
        <ul class="dropdown-menu">
            {foreach from=$modulecustombuttons key=label item=command}
            <li><a href="clientarea.php?action=productdetails&amp;id={$id}&amp;modop=custom&amp;a={$command}">{$label}</a></li>
            {/foreach}
            {if $packagesupgrade}<li><a href="upgrade.php?type=package&amp;id={$id}">{$LANG.upgradedowngradepackage}</a></li>{/if}
            {if $configoptionsupgrade}<li><a href="upgrade.php?type=configoptions&amp;id={$id}">{$LANG.upgradedowngradeconfigoptions}</a></li>{/if}
            {if $showcancelbutton}<li><a href="clientarea.php?action=cancel&amp;id={$id}">{$LANG.clientareacancelrequestbutton}</a></li>{/if}
        </ul>
    </li>{/if}
</ul>
</div>

<div id="tab1" class="tab-content">

    <div class="row">  
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="pull-right"><input type="button" value="{$LANG.backtoserviceslist}" class="btn btn-default btn-sm" onclick="window.location='clientarea.php?action=products'" /></div>
                    <h3 class="panel-title"><span aria-hidden="true" class="icon icon-info"></span> {$LANG.information}</h3><small>{$LANG.clientareaproductdetailsintro}</small></div>
                    <div class="panel-body">
                        <h4>{$LANG.clientareahostingregdate}: {$regdate}</h4>
                        <h4>{$LANG.orderproduct}: {$groupname} - {$product} <span class="label {$rawstatus}">{$status}</span></h4>{if $domain}<p><a href="http://{$domain}" target="_blank">{$domain}</a></p>{/if}
                        {if $dedicatedip}
                        <h4>{$LANG.domainregisternsip}: {$dedicatedip}</h4>
                        <div class="clear"></div>
                        {/if}
                        {foreach from=$configurableoptions item=configoption}
                        <div class="col-lg-12"><div class="well well-sm">
                            <h4>{$configoption.optionname}:</h4> <span>{if $configoption.optiontype eq 3}{if $configoption.selectedqty}{$LANG.yes}{else}{$LANG.no}{/if}{elseif $configoption.optiontype eq 4}{$configoption.selectedqty} x {$configoption.selectedoption}{else}{$configoption.selectedoption}{/if}</span>
                        </div></div>
                        {/foreach}
                        <div class="row">
                            {if $firstpaymentamount neq $recurringamount}
                            <div class="col-lg-12"><div class="well well-sm">
                                <h4>{$LANG.firstpaymentamount}:</h4> <span>{$firstpaymentamount}</span>
                            </div></div>
                            {/if}
                            <div class="col-lg-3"><div class="well well-sm">
                                <h4>{$LANG.recurringamount}:</h4> <span>{$recurringamount}</span>
                            </div></div>
                            <div class="col-lg-3"><div class="well well-sm">
                                <h4>{$LANG.orderbillingcycle}:</h4> <span>{$billingcycle}</span>
                            </div></div>
                            <div class="col-lg-3"><div class="well well-sm">
                               <h4>{$LANG.clientareahostingnextduedate}:</h4> <span>{$nextduedate}</span>
                           </div></div>
                           <div class="col-lg-3"><div class="well well-sm">
                               <h4>{$LANG.orderpaymentmethod}:</h4> <span>{$paymentmethod}</span>
                           </div></div></div>
                           {if $suspendreason}
                           <h4>{$LANG.suspendreason}:<span>{$suspendreason}</span></h4> 
                           {/if}
                           {if $lastupdate}
                           <div class="row">
                               <div class="col-lg-6">
                                   <h4>{$LANG.clientareadiskusage}:</h4>
                                   <span>{$diskusage}MB / {$disklimit}MB ({$diskpercent})</span>
                                   <div class="progress">
                                       <div class="progress-bar" role="progressbar" aria-valuenow="{$diskpercent}" aria-valuemin="0" aria-valuemax="100" style="width: {$diskpercent};">
                                           <span class="sr-only">{$LANG.clientareadiskusage}</span>
                                       </div>
                                   </div>
                               </div>
                               <div class="col-lg-6">
                                <h4>{$LANG.clientareabwusage}:</h4>
                                <span>{$bwusage}MB / {$bwlimit}MB ({$bwpercent})</span>
                                <div class="progress">
                                   <div class="progress-bar" role="progressbar" aria-valuenow="{$bwpercent}" aria-valuemin="0" aria-valuemax="100" style="width: {$bwpercent};">
                                       <span class="sr-only">{$LANG.clientareabwusage}</span>
                                   </div>
                               </div>
                           </div>
                       </div>
                       {/if}
                       <div class="row">                      
                        {foreach from=$productcustomfields item=customfield}
                        <div class="col-md-4">
                            <div class="well well-sm">
                                <h5>{$customfield.name}:</h5>
                                <p>{$customfield.value}</p>
                            </div>
                        </div>
                        {/foreach}
                    </div> 
                    {if $moduleclientarea}<div class="moduleoutput">{$moduleclientarea|replace:'modulebutton':'btn btn-default'}</div>{/if}
                </div>
            </div>
        </div> 
    </div>
</div>
<div id="tab2" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2>{$LANG.serverchangepassword}</h2>
            <p><small>{$LANG.serverchangepasswordintro}</small></p>
        </div>
        <div class="col-lg-8">
            <form method="post" action="{$smarty.server.PHP_SELF}?action=productdetails#tab2">
                <input type="hidden" name="id" value="{$id}" />
                <input type="hidden" name="modulechangepassword" value="true" />

                {if $username}<div class="form-group">
                <label class="control-label" for="password">{$LANG.serverusername}/{$LANG.serverpassword}</label>
                {$username}{if $password} / {$password}{/if}
            </div>{/if}

            <div class="form-group">
             <label class="control-label" for="password">{$LANG.newpassword}</label>
             <input type="password" class="form-control" name="newpw" id="password" />
         </div>

         <div class="form-group">
             <label class="control-label" for="confirmpw">{$LANG.confirmnewpassword}</label>
             <input type="password" class="form-control" name="confirmpw" id="confirmpw" />
         </div>

         <div class="form-group">
             <label class="control-label" for="passstrength">{$LANG.pwstrength}</label>
             {include file="$template/pwstrength.tpl"}
         </div>
  <div class="btn-toolbar pull-right" role="toolbar">
    <input class="btn btn-primary btn-sm pull-right" type="submit" name="submit" value="{$LANG.clientareasavechanges}" />
    <input class="btn btn-link btn-sm pull-right" type="reset" value="{$LANG.cancel}" />
    </div>
     </form>
 </div>
</div>
</div>
<div id="tab3" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2>{$LANG.downloadstitle}</h2>
            <p>{$LANG.clientareahostingaddonsintro}</p>
        </div>
        <div class="col-lg-8">
            {foreach from=$downloads item=download}
            <p><h4>{$download.title} - <a href="{$download.link}">{$LANG.downloadname}</a></h4> {$download.description}</p>
            {/foreach}
        </div>

    </div>

</div>
<div id="tab4" class="tab-content">
    <div class="row">
        <div class="col-lg-4">
            <h2><span aria-hidden="true" class="icon icon-plus"></span> {$LANG.clientareahostingaddons}</h2>
            <p><small>{$LANG.yourclientareahostingaddons}</small></p>
            {if $addonsavailable}<p><a href="cart.php?gid=addons&pid={$id}">{$LANG.orderavailableaddons}</a></p>{/if}
        </div>
        <div class="col-lg-8">
            <table class="table table-striped table-framed table-centered">
                <thead>
                    <tr>
                        <th>{$LANG.clientareaaddon}</th>
                        <th>{$LANG.clientareaaddonpricing}</th>
                        <th>{$LANG.clientareahostingnextduedate}</th>
                        <th>{$LANG.clientareastatus}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach key=num item=addon from=$addons}
                    <tr>
                        <td>{$addon.name}</td>
                        <td class="textcenter">{$addon.pricing}</td>
                        <td class="textcenter">{$addon.nextduedate}</td>
                        <td class="textcenter"><span class="label {$addon.rawstatus}">{$addon.status}</span></td>
                    </tr>
                    {foreachelse}
                    <tr>
                        <td class="textcenter" colspan="4">{$LANG.clientareanoaddons}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
