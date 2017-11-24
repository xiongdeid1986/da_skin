{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<p>{$LANG.downloadsintrotext}</p>

<div class="padding-25">
    <div class="row">
		<div class="col-md-7 center-block">
			<form method="post" action="downloads.php?action=search">
				<div class="input-group">
					<input type="text" name="search" value="{$LANG.downloadssearch}" class="form-control input-lg" onfocus="if(this.value=='{$LANG.downloadssearch}')this.value=''" />
					<span class="input-group-btn">
						<button type="submit" class="btn btn-lg btn-inverse"><i class="fa fa-search icon-only"></i></button>
					</span>
					</div>
			</form>
		</div>
	</div>
</div>

<div class="block-s3">
	<h3>{$LANG.downloadscategories}</h4>
	<div class="row">
	{foreach from=$dlcats item=dlcat}
		<div class="col-sm-4">
			<div class="well white">
				<h4><a href="{if $seofriendlyurls}downloads/{$dlcat.id}/{$dlcat.urlfriendlyname}{else}downloads.php?action=displaycat&amp;catid={$dlcat.id}{/if}">{$dlcat.name}</a> ({$dlcat.numarticles})</h4>
					{$dlcat.description}
			</div>
		</div>
	{/foreach}
	</div>
</div>

<div class="block-s2">
`	<h3>{$LANG.downloadspopular}</h3>
	{foreach from=$mostdownloads item=download}
		<h5>{$download.type} <a href="{$download.link}">{$download.title}{if $download.clientsonly} <img src="images/padlock.gif" alt="{$LANG.loginrequired}" />{/if}</a></h5>
		<div>{$download.description}</div>
		<small>{$LANG.downloadsfilesize}: {$download.filesize}</small>
	{/foreach}
</div>