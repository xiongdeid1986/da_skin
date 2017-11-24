{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


<div class="padding-25">
    <div class="row">
		<div class="col-md-7 center-block">
        <form method="post" action="knowledgebase.php?action=search">
			 {if $catid}<input type="hidden" name="catid" value="{$catid}" />{/if}
            <div class="input-group">
                <input class="form-control input-lg" name="search" type="text" value="{$LANG.kbquestionsearchere}" onfocus="this.value=(this.value=='{$LANG.kbquestionsearchere}') ? '' : this.value;" onblur="this.value=(this.value=='') ? '{$LANG.kbquestionsearchere}' : this.value;"/>
                <span class="input-group-btn">
					<button type="submit" class="btn btn-lg btn-inverse" value="" /><i class="fa fa-search"></i><span class="hidden-xs">{$LANG.knowledgebasesearch}</span></button>
				</span>
            </div>
        </form>
		</div>
    </div>
</div>

{if $kbcats}
<div class="block-s3">
	<h3>{$LANG.knowledgebasecategories}</h3>
	<div class="row">
		{foreach name=kbasecats from=$kbcats item=kbcat}
			<div class="col-sm-4">
				<div class="well white">
				<h4><a href="{if $seofriendlyurls}knowledgebase/{$kbcat.id}/{$kbcat.urlfriendlyname}{else}knowledgebase.php?action=displaycat&amp;catid={$kbcat.id}{/if}">{$kbcat.name}</a> <small>({$kbcat.numarticles})</small></h4>
				{$kbcat.description}
				</div>
			</div>
		{/foreach}
	</div>
</div>
{/if}

{if $kbarticles}
<div class="block-s3">


	<h3>{$LANG.knowledgebasearticles}</h3>
	{foreach from=$kbarticles item=kbarticle}
		<p>
			<a href="{if $seofriendlyurls}knowledgebase/{$kbarticle.id}/{$kbarticle.urlfriendlytitle}.html{else}knowledgebase.php?action=displayarticle&amp;id={$kbarticle.id}{/if}">{$kbarticle.title}</a><br />
			{$kbarticle.article|truncate:100:"..."}
		</p>
	{/foreach}

</div>
{else}
<br />
<p>{$LANG.knowledgebasenoarticles}</p>
<br /><br /><br />
{/if}
<div class="clear"></div>

<br />
