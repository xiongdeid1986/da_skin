{include file="$template/pageheader.tpl" title=$LANG.navopenticket}
<p>{$LANG.supportticketsheader}</p>
<div class="row">
    {foreach from=$departments item=department}
        <div class="col-lg-6">
        <div class="panel panel-default">
         <div class="panel-body">
                <h4><span aria-hidden="true" class="icon-envelope"></span> <a href="{$smarty.server.PHP_SELF}?step=2&amp;deptid={$department.id}">{$department.name}</a></h4>
    			{if $department.description}<p>{$department.description}</p>{/if}
        </div>
        </div>
        </div>
    {foreachelse}
    <div class="alert alert-block alert-info">
        {$LANG.nosupportdepartments}
    </div>
    {/foreach}
</div>