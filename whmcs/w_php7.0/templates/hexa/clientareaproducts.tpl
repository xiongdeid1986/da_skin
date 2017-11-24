<h3 class="page-header"><span aria-hidden="true" class="icon icon-layers"></span> {$LANG.clientareaproducts} <i class="fa fa-info-circle animated bounce show-info"></i></h3>
            <blockquote class="page-information hidden">
                <p>{$LANG.clientareaproductsintro}</p>
            </blockquote>
    <form method="post" class="form-inline" action="clientarea.php?action=products">
<div class="row">
 <div class="col-lg-4">
 <div class="input-group">
            <input type="text" class="form-control" name="q" value="{if $q}{$q}{else}{$LANG.searchenterdomain}{/if}" class="input-medium appendedInputButton" onfocus="if(this.value=='{$LANG.searchenterdomain}')this.value=''" />
      <span class="input-group-btn">
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
      </span>
            </div>
    <span class="help-block"><small>{$numitems} {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</small></span>
  </div>
</div>        
    </form>

<table class="table table-striped table-framed table-hover">
    <thead>
        <tr>
            <th{if $orderby eq "product"} class="headerSort{$sort}"{/if}><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=product">{$LANG.orderproduct}</a></th>
            <th{if $orderby eq "price"} class="headerSort{$sort} hidden-sm hidden-xs"{else} class="hidden-sm hidden-xs"{/if}><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=price">{$LANG.orderprice}</a></th>
            <th{if $orderby eq "billingcycle"} class="headerSort{$sort} hidden-sm hidden-xs"{else} class="hidden-sm hidden-xs"{/if}><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=billingcycle">{$LANG.orderbillingcycle}</a></th>
            <th{if $orderby eq "nextduedate"} class="headerSort{$sort} hidden-xs"{else} class="hidden-xs"{/if}><a href="clientarea.php?action=products{if $q}&q={$q}{/if}&orderby=nextduedate">{$LANG.clientareahostingnextduedate}</a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$services item=service}
        <tr>
            <td><span class="label {$service.rawstatus}">{$service.statustext}</span><strong> {$service.group} - {$service.product}</strong>{if $service.domain} <a href="http://{$service.domain}" target="_blank">{$service.domain}</a>{/if}
            <ul class="cell-inner-list visible-sm visible-xs">
            <li><span class="item-title">{$LANG.orderbillingcycle} : </span>{$service.billingcycle}</li>
            <li><span class="item-title">{$LANG.clientareahostingnextduedate}</span> : {$service.nextduedate}</li>                                                              
            <li><span class="item-title">{$LANG.orderprice} : </span>{$service.amount}</li>
            </ul>
            </td>
            <td class="hidden-sm hidden-xs">{$service.amount}</td>
            <td class="hidden-sm hidden-xs">{$service.billingcycle}</td>
            <td class="hidden-xs">{$service.nextduedate}</td>
            <td>
                <div class="btn-group btn-group-xs pull-right">
                <a class="btn btn-default" href="clientarea.php?action=productdetails&id={$service.id}"><span aria-hidden="true" class="icon icon-list"></span> {$LANG.clientareaviewdetails}</a>
                {if $service.rawstatus == "active" && ($service.downloads || $service.addons || $service.packagesupgrade || $service.showcancelbutton)}
                <a class="btn btn-default dropdown-toggle hidden-xs" href="#" data-toggle="dropdown"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    {if $service.downloads} <li><a href="clientarea.php?action=productdetails&id={$service.id}#tab3"><span class="glyphicon glyphicon-download"></span> {$LANG.downloadstitle}</a></li>{/if}
                    {if $service.addons} <li><a href="clientarea.php?action=productdetails&id={$service.id}#tab4"><span class="glyphicon glyphicon-th-large"></span> {$LANG.clientareahostingaddons}</a></li>{/if}
                    {if $service.packagesupgrade} <li><a href="upgrade.php?type=package&id={$service.id}#tab3"><span class="glyphicon glyphicon-resize-vertical"></span> {$LANG.upgradedowngradepackage}</a></li>{/if}
                    {if ($service.addons || $service.downloads || $service.packagesupgrade) && $service.showcancelbutton} <li class="divider"></li>{/if}
                    {if $service.showcancelbutton} <li><a href="clientarea.php?action=cancel&id={$service.id}"><span class="glyphicon glyphicon-off"></span> {$LANG.clientareacancelrequestbutton}</a></li>{/if}
                </ul>
                {/if}
                </div>
            </td>
        </tr>
{foreachelse}
        <tr>
            <td colspan="5" class="textcenter">{$LANG.norecordsfound}</td>
        </tr>
{/foreach}
    </tbody>
</table>

{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

    <ul class="pagination">
        <li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=products{if $q}&q={$q}{/if}&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
        <li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=products{if $q}&q={$q}{/if}&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
    </ul>