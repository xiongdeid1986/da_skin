<div class="col-lg-12">
{include file="$template/pageheader.tpl" title=$LANG.accessdenied}
<p>{$LANG.bannedyourip} {$ip} {$LANG.bannedhasbeenbanned}</p>
<div class="alert alert-danger">
    <ul>
        <li>{$LANG.bannedbanreason}: <strong>{$reason}</strong></li>
    	<li>{$LANG.bannedbanexpires}: {$expires}</li>
    </ul>
</div>
</div>