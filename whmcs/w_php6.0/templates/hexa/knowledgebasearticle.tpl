{include file="$template/pageheader.tpl" title=$LANG.knowledgebasetitle}
<script>
	function addBookmark() {ldelim}
	if (window.sidebar) {ldelim}
		window.sidebar.addPanel('{$companyname} - {$kbarticle.title}', location.href,"");
	{rdelim} else if( document.all ) {ldelim}
	window.external.AddFavorite( location.href, '{$companyname} - {$kbarticle.title}');
	{rdelim} else if( window.opera && window.print ) {ldelim}
	return true;
	{rdelim}
	{rdelim}
</script>
<p><small>{$breadcrumbnav}</small></p>
<h3>{$kbarticle.title}</h3>
{$kbarticle.text}
<div class="info-aa">
    <a href="http://cmsbased.net">cmsbased.net</a>
</div>
<hr>
<div class="row">
	<div class="col-lg-6">
		<form method="post" class="form-inline" action="knowledgebase.php?action=displayarticle&amp;id={$kbarticle.id}&amp;useful=vote">
			{if $kbarticle.voted}
			{$LANG.knowledgebaserating} {$kbarticle.useful} {$LANG.knowledgebaseratingtext} <span class="badge">{$kbarticle.votes} {$LANG.knowledgebasevotes}</span>
			{else}
			<label class="control-label">{$LANG.knowledgebasehelpful} </label><div class="form-group"><select class="form-control input-sm" name="vote"><option value="yes">{$LANG.knowledgebaseyes}</option><option value="no">{$LANG.knowledgebaseno}</option></select></div> <input type="submit" value="{$LANG.knowledgebasevote}" class="btn btn-default btn-sm" />
			{/if}
		</form>
	</div>
	<div class="col-lg-6">
		<div class="pull-right">
			<a class="btn btn-default btn-sm" href="#" onClick="addBookmark();return false"><span class="glyphicon glyphicon-star"></span> {$LANG.knowledgebasefavorites}</a> <a href="#" class="btn btn-default btn-sm" onclick="window.print();return false"><span class="glyphicon glyphicon-print"></span> {$LANG.knowledgebaseprint}</a>
		</div>
	</div>
</div>
{if $kbarticles}
<h3><span class="glyphicon glyphicon-paperclip"></span> {$LANG.knowledgebasealsoread}</h3>
<ul class="list-group">
	{foreach key=num item=kbarticle from=$kbarticles}
	<li class="list-group-item"><span class="glyphicon glyphicon-file"></span> <a href="{if $seofriendlyurls}knowledgebase/{$kbarticle.id}/{$kbarticle.urlfriendlytitle}.html{else}knowledgebase.php?action=displayarticle&amp;id={$kbarticle.id}{/if}">{$kbarticle.title}</a><span class="badge">{$LANG.knowledgebaseviews}: {$kbarticle.views}</span>
	</li>
	{/foreach}
</ul>
{/if}