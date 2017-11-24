{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{foreach key=num item=announcement from=$announcements}
<div class="block-s3 no-padding-top">
<h3><a href="{if $seofriendlyurls}announcements/{$announcement.id}/{$announcement.urlfriendlytitle}.html{else}{$smarty.server.PHP_SELF}?id={$announcement.id}{/if}" class="">{$announcement.title}</a></h3>
<p><i class="fa fa-calendar text-success"></i> &nbsp;{$announcement.timestamp|date_format:"%A, %B %e, %Y"}</p>
<p>{$announcement.text|strip_tags|truncate:400:"..."}</p>
{if strlen($announcement.text)>300}<p><div class="action-buttons"><a href="{if $seofriendlyurls}announcements/{$announcement.id}/{$announcement.urlfriendlytitle}.html{else}{$smarty.server.PHP_SELF}?id={$announcement.id}{/if}" >{$LANG.more} <i class="fa fa-angle-double-right"></i></a></div></p>{/if}
</div>

<div class="hr hr-6 dotted hr-double"></div>

{if $facebookrecommend}
{literal}
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
{/literal}
<div class="fb-like" data-href="{$systemurl}{if $seofriendlyurls}announcements/{$announcement.id}/{$announcement.urlfriendlytitle}.html{else}announcements.php?id={$announcement.id}{/if}" data-send="true" data-width="450" data-show-faces="true" data-action="recommend"></div>
{/if}
<br /><br />
{foreachelse}
<p align="center"><strong>{$LANG.announcementsnone}</strong></p>
{/foreach}

<br />
<div class="action-buttons pull-right"><a href="announcementsrss.php"><i class="fa fa-rss text-orange"></i> {$LANG.announcementsrss}</a></div>
<div class="clearfix"></div>

   <ul class="pagination">
      <li{if !$prevpage} class="disabled"{/if}>
			<a href="{if $prevpage}{$smarty.server.PHP_SELF}?page={$prevpage}{else}javascript:return false;{/if}" title="{$LANG.previouspage}">&larr; {$LANG.previouspage}</a>
      </li>
      <li{if !$nextpage} class="disabled"{/if}>
			<a href="{if $nextpage}{$smarty.server.PHP_SELF}?page={$nextpage}{else}javascript:return false;{/if}" title="{$LANG.nextpage}">{$LANG.nextpage} &rarr;</a>
      </li>
   </ul>

<br />