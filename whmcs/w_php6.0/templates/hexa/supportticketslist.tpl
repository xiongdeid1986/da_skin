<h3 class="page-header"><span aria-hidden="true" class="icon icon-support"></span> {$LANG.clientareanavsupporttickets} <i class="fa fa-info-circle animated bounce show-info"></i></h3>      <blockquote class="page-information hidden">
                <p>{$LANG.supportticketsintro}</p>
            </blockquote>

<form method="post" class="form-inline" action="supporttickets.php">
    <div class="row">
       <div class="col-lg-4">
           <div class="input-group">
            <input type="text" class="form-control"  name="searchterm" value="{if $q}{$q}{else}{$LANG.searchtickets}{/if}" class="input-medium appendedInputButton" onfocus="if(this.value=='{$LANG.searchtickets}')this.value=''" />
            <span class="input-group-btn">
                <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
            </span>
        </div>
        <span class="help-block"><small>{$numitems} {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</small></span>
    </div>
      <div class="col-lg-8">
         <a class="btn btn-default btn-sm pull-right" href="submitticket.php"><span aria-hidden="true" class="icon-plus"></span> {$LANG.opennewticket}</a>
     </div>
</div>        
</form>
<table class="table table-striped table-framed table-hover">
    <thead>
        <tr>
            <th{if $orderby eq "dept"} class="headerSort{$sort} hidden-sm hidden-xs"{else} class="hidden-sm hidden-xs"{/if}><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=dept">{$LANG.supportticketsdepartment}</a></th>
            <th{if $orderby eq "subject"} class="headerSort{$sort} "{/if}><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=subject">{$LANG.supportticketssubject}</a></th>
            <th{if $orderby eq "status"} class="headerSort{$sort} hidden-sm hidden-xs"{else} class="hidden-sm hidden-xs"{/if}><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=status">{$LANG.supportticketsstatus}</a></th>
            <th{if $orderby eq "lastreply"} class="headerSort{$sort} hidden-sm hidden-xs"{else} class="hidden-sm hidden-xs"{/if}><a href="supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}orderby=lastreply">{$LANG.supportticketsticketlastupdated}</a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        {foreach key=num item=ticket from=$tickets}
        <tr>
            <td class="hidden-sm hidden-xs">{$ticket.department}</td>
            <td><div align="left"><i class="icon-file-text-alt"></i> <a href="viewticket.php?tid={$ticket.tid}&amp;c={$ticket.c}">{if $ticket.unread}<strong>{/if}#{$ticket.tid} - {$ticket.subject}{if $ticket.unread}</strong>{/if}</a></div>
                <ul class="cell-inner-list visible-sm visible-xs">
                    <li><span class="item-title">{$LANG.supportticketsticketid} : </span># {$ticket.tid}</li>
                    <li><span class="item-title">{$LANG.supportticketsticketlastupdated} : </span>{$ticket.lastreply}</li>
                    <li><span class="item-title">{$LANG.supportticketsdepartment} : </span>{$ticket.department}</li>
                    <li><span class="item-title">{$LANG.supportticketsticketurgency} : </span>{$ticket.urgency}</li>                                                        
                    <li><span class="item-title">{$LANG.supportticketsstatus} : {$ticket.status}</li>
                </ul>
            </td>
            <td class="hidden-sm hidden-xs">{$ticket.status}</td>
            <td class="hidden-sm hidden-xs">{$ticket.lastreply}</td>
            <td><a href="viewticket.php?tid={$ticket.tid}&c={$ticket.c}"<span class="glyphicon glyphicon-chevron-right pull-right"></span></a></td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="7" class="textcenter">{$LANG.norecordsfound}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{include file="$template/clientarearecordslimit.tpl" clientareaaction=$clientareaaction}

<ul class="pagination">
    <li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
    <li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}supporttickets.php?{if $searchterm}searchterm={$searchterm}&token={$token}&{/if}page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>
