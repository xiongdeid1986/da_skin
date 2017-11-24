{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<ul class="list-unstyled">
{foreach from=$tweets item=tweet key=num}
{if $num < $numtweets}
  <li><strong>{$tweet.date}</strong> - {$tweet.tweet}</li>
{/if}
{/foreach}
</ul>

<p><a href="http://twitter.com/{$twitterusername}" target="_blank" class="btn btn-twitter"><i class="fa fa-twitter"></i>{$LANG.twitterfollowus}</a></p>
