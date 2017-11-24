    <div class="row">
        <div class="col-md-12">

            <h3 class="page-header"><span aria-hidden="true" class="icon icon-envelope"></span> {$LANG.clientareaemails} <i class="fa fa-info-circle animated bounce show-info"></i> </h3>

            <blockquote class="page-information hidden">
                <p>{$LANG.emailstagline}</p>
            </blockquote>
        </div>
    </div>


<p>{$numitems} {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>
<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <th{if $orderby eq "date"} class="headerSort{$sort}"{/if}><a href="clientarea.php?action=emails&orderby=date">{$LANG.clientareaemailsdate}</a></th>
            <th{if $orderby eq "subject"} class="headerSort{$sort}"{/if}><a href="clientarea.php?action=emails&orderby=subject">{$LANG.clientareaemailssubject}</a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
{foreach from=$emails item=email}
        <tr>
            <td>{$email.date}</td>
            <td>{$email.subject}</td>
            <td><input type="button" class="btn btn-default btn-sm pull-right" value="{$LANG.emailviewmessage}" onclick="popupWindow('viewemail.php?id={$email.id}','emlmsg',650,400)" /></td>
        </tr>
{foreachelse}
        <tr>
            <td colspan="3" class="textcenter">{$LANG.norecordsfound}</td>
        </tr>
{/foreach}
    </tbody>
</table>
    <ul class="pagination">
        <li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}clientarea.php?action=emails&amp;page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
        <li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}clientarea.php?action=emails&amp;page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
    </ul>
