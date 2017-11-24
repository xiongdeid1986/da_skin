<h4><span class="glyphicon glyphicon-warning-sign text-danger"></span> {$LANG.kbsuggestions}</h4>
<p><small>{$LANG.kbsuggestionsexplanation}</small></p>
<p>{foreach from=$kbarticles item=kbarticle}
<span class="glyphicon glyphicon-book"></span> <a href="knowledgebase.php?action=displayarticle&id={$kbarticle.id}" target="_blank">{$kbarticle.title}</a> - {$kbarticle.article}...<br>
{/foreach}</p>