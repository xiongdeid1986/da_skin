{if $affiliatesystemenabled}
    <div class="row">
      <div class="col-md-12">
        <h3 class="page-header"><span aria-hidden="true" class="icon icon-users"></span> {$LANG.affiliatesactivate} </h3>
      </div>
    </div>

<div class="row">
<div class="col-md-4">   
<div class="well">
    <h5>{$LANG.affiliatesignuptitle}</h5>
    <p>{$LANG.affiliatesignupintro}</p>
</div>
</div>
<div class="col-md-8">   
<ul class="list-group">
  <li class="list-group-item">{$LANG.affiliatesignupinfo1}</li>
  <li class="list-group-item">{$LANG.affiliatesignupinfo2}</li>
  <li class="list-group-item">{$LANG.affiliatesignupinfo3}</li>
</ul>
</div>
</div>
<form method="post" action="affiliates.php">
<input type="hidden" name="activate" value="true" />
<input type="submit" value="{$LANG.affiliatesactivate}" class="btn btn-default btn-sm pull-right" />
</form>
{else}
   <div class="row">
      <div class="col-md-12">
        <h3 class="page-header"><span aria-hidden="true" class="icon icon-users"></span> {$LANG.affiliatestitle} </h3>
      </div>
    </div>
<div class="alert alert-warning">
<p>{$LANG.affiliatesdisabled}</p>
</div>
{/if}
