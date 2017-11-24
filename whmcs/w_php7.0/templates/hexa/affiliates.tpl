{if $inactive}
    <div class="row">
      <div class="col-md-12">
        <h3 class="page-header"><span aria-hidden="true" class="icon icon-users"></span> {$LANG.affiliatestitle} </h3>
      </div>
    </div>
<div class="alert alert-warning">
  <p>{$LANG.affiliatesdisabled}</p>
</div>
{else}
    <div class="row">
      <div class="col-md-12">
        <h3 class="page-header"><span aria-hidden="true" class="icon icon-users"></span> {$LANG.affiliatestitle} <i class="fa fa-info-circle animated bounce show-info"></i></h3>
                <blockquote class="page-information hidden">
          <p>{$LANG.affiliatesrealtime}</p>
        </blockquote>      
      </div>
    </div>
<div class="row">
  <div class="col-lg-12">
    <h4><span aria-hidden="true" class="icon icon-link"></span> {$LANG.affiliatesreferallink}</h4>
    <div class="form-group">
      <input type="text" value="{$referrallink}" class="form-control" />
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliatesvisitorsreferred}
      <span class="label label-default pull-right">{$visitors}</span>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliatessignups}
      <span class="label label-success pull-right">{$signups}</span>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliatesconversionrate}
      <span class="label label-info pull-right">{$conversionrate}%</span>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliatescommissionspending}
      <span class="label label-default pull-right">{$pendingcommissions}</span>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliatescommissionsavailable}
      <span class="label label-success pull-right">{$balance}</span>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="well">
      {$LANG.affiliateswithdrawn}
      <span class="label label-info pull-right">{$withdrawn}</span>
    </div>
  </div>
</div>


{if $withdrawrequestsent}
<div class="alert alert-success">
  <p>{$LANG.affiliateswithdrawalrequestsuccessful}</p>
</div>
{else}
{if $withdrawlevel}
<input type="button" class="btn btn-default btn-sm" value="{$LANG.affiliatesrequestwithdrawal}" onclick="window.location='{$smarty.server.PHP_SELF}?action=withdrawrequest'" />
{/if}
{/if}

{include file="$template/subheader.tpl" title=$LANG.affiliatesreferals}

<p>{$numitems} {$LANG.recordsfound}, {$LANG.page} {$pagenumber} {$LANG.pageof} {$totalpages}</p>

<table class="table table-striped table-framed">
  <thead>
    <tr>
      <th{if $orderby eq "date"} class="headerSort{$sort}"{/if}><a href="affiliates.php?orderby=date">{$LANG.affiliatessignupdate}</a></th>
      <th{if $orderby eq "product"} class="headerSort{$sort}"{/if}><a href="affiliates.php?orderby=product">{$LANG.orderproduct}</a></th>
      <th{if $orderby eq "amount"} class="headerSort{$sort}"{/if}><a href="affiliates.php?orderby=amount">{$LANG.affiliatesamount}</a></th>
      <th>{$LANG.affiliatescommission}</th>
      <th{if $orderby eq "status"} class="headerSort{$sort}"{/if}><a href="affiliates.php?orderby=status">{$LANG.affiliatesstatus}</a></th>
    </tr>
  </thead>
  <tbody>
    {foreach key=num item=referral from=$referrals}
    <tr>
      <td>{$referral.date}</td>
      <td>{$referral.service}</td>
      <td>{$referral.amountdesc}</td>
      <td>{$referral.commission}</td>
      <td>{$referral.status}</td>
    </tr>
    {foreachelse}
    <tr>
      <td colspan="5">{$LANG.norecordsfound}</td>
    </tr>
    {/foreach}
  </tbody>
</table>

<ul class="pagination">
  <li class="prev{if !$prevpage} disabled{/if}"><a href="{if $prevpage}affiliates.php?page={$prevpage}{else}javascript:return false;{/if}">&larr; {$LANG.previouspage}</a></li>
  <li class="next{if !$nextpage} disabled{/if}"><a href="{if $nextpage}affiliates.php?page={$nextpage}{else}javascript:return false;{/if}">{$LANG.nextpage} &rarr;</a></li>
</ul>

{if $affiliatelinkscode}
{include file="$template/subheader.tpl" title=$LANG.affiliateslinktous}
<div class="textcenter">
  {$affiliatelinkscode}
</div>
{/if}

{/if}
