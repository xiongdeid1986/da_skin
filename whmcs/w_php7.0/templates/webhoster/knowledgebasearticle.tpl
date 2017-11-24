{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<div class="pull-right">
	<a href="#" onClick="addBookmark();return false"><img src="images/addtofavouritesicon.gif" class="valignbaseline" alt="{$LANG.knowledgebasefavorites}"></a> &nbsp;&nbsp; <a href="#" onclick="window.print();return false"><img src="images/print.gif" class="valignbaseline" alt="{$LANG.knowledgebaseprint}"></a>
</div>

{literal}
<script type="text/javascript">
function addBookmark() {
	if (window.sidebar) {
		window.sidebar.addPanel('{/literal}{$companyname} - {$kbarticle.title}{literal}', location.href,"");
	} else if( document.all ) {
		window.external.AddFavorite( location.href, {/literal}'{$companyname} - {$kbarticle.title}'{literal});
	} else if( window.opera && window.print ) {
		return true;
	}
}
</script>
{/literal}

<h2>{$kbarticle.title}</h2>
<div class="hr hr-6 dotted hr-double"></div>

<div class="block-s2 no-padding-top">
	{$kbarticle.text}
</div>

<div class="block-s3 no-padding-bottom">
<form method="post" action="knowledgebase.php?action=displayarticle&amp;id={$kbarticle.id}&amp;useful=vote" class="form-horizontal well white well-sm">
	{if $kbarticle.voted}
		<strong>{$LANG.knowledgebaserating}</strong> {$kbarticle.useful} {$LANG.knowledgebaseratingtext} ({$kbarticle.votes} {$LANG.knowledgebasevotes})
	{else}
		<div class="form-group no-margin-bottom">
    		<label for="vote" class="col-sm-3 col-md-3 control-label">{$LANG.knowledgebasehelpful}</label>
    		<div class="col-sm-9 col-md-9">
    				<div id="radioBtn" class="btn-group btn-group-sm">
    					<a class="btn btn-primary active" data-toggle="vote" data-title="yes">{$LANG.knowledgebaseyes}</a>	
    					<a class="btn btn-primary notActive" data-toggle="vote" data-title="no">{$LANG.knowledgebaseno}</a>
						<input type="hidden" name="vote" id="vote" value="yes">
						<button type="submit" class="btn btn-success icon-only"><i class="fa fa-send"></i> Send</button>
					</div>
    			</div>
    	</div>
	{/if}
</form>
</div>

{if $kbarticles}

<div class="block-s3 no-padding-top">
	<div class="">
		<h3>{$LANG.knowledgebasealsoread}</h3>
		{foreach key=num item=kbarticle from=$kbarticles}
			<p>
				<a href="{if $seofriendlyurls}knowledgebase/{$kbarticle.id}/{$kbarticle.urlfriendlytitle}.html{else}knowledgebase.php?action=displayarticle&amp;id={$kbarticle.id}{/if}">{$kbarticle.title}</a> <small>({$LANG.knowledgebaseviews}: {$kbarticle.views})</small>
			</p>
		{/foreach}
	</div>
</div>

{/if}