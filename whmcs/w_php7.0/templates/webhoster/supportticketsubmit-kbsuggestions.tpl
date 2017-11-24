{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


<br /><br />
<h4><i class="fa fa-start-o"></i> {$LANG.kbsuggestions}</h4>
<div class="hr hr8 hr-dotted"></div>
<p>{$LANG.kbsuggestionsexplanation}</p>

<p>{foreach from=$kbarticles item=kbarticle}
<i class="fa fa-file-text"></i> <a href="knowledgebase.php?action=displayarticle&id={$kbarticle.id}" target="_blank">{$kbarticle.title}</a> - {$kbarticle.article}...<br>
{/foreach}</p>