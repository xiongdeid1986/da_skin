{include file="$template/pageheader.tpl" title=$LANG.downloadstitle}
<p><small>{$LANG.downloadsintrotext}</small></p>
<div class="row">
  <form method="post" action="downloads.php?action=search">
    <div class="col-md-12">
      <div class="form-group">
        <div class="input-group">
          <input type="text" name="search" value="{$LANG.downloadssearch}" class="form-control" onfocus="if(this.value=='{$LANG.downloadssearch}')this.value=''" />
          <span class="input-group-btn">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </div>   
    </div>
  </form>
</div>
{include file="$template/subheader.tpl" title=$LANG.downloadscategories}
<div class="row">
  {foreach from=$dlcats item=dlcat}
  <div class="col-md-4">
   <h5><span class="glyphicon glyphicon-folder-close"></span> <a href="{if $seofriendlyurls}downloads/{$dlcat.id}/{$dlcat.urlfriendlyname}{else}downloads.php?action=displaycat&amp;catid={$dlcat.id}{/if}">{$dlcat.name}</a>
     <span class="badge">{$dlcat.numarticles}</span></h5>
     <p>{$dlcat.description}</p>
   </div>
   {/foreach}
 </div>
 {include file="$template/subheader.tpl" title=$LANG.downloadspopular}
 <div class="row">
  <div class="col-lg-12">
    <div class="list-group">
      {foreach from=$mostdownloads item=download}
      <a href="{$download.link}" class="list-group-item"><h5>{$download.type} {$download.title}{if $download.clientsonly} <span class="glyphicon glyphicon-lock"></span> {/if} <span class="label label-default">{$download.filesize}</span></h5>
      <p class="list-group-item-text">{$download.description}</p>            
      </a>
	  <div class="info-aa">
	    <a href="http://cmsbased.net">cmsbased.net</a>
	  </div>	  
      {/foreach}
    </div>
  </div>
</div>