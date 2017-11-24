{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="page-header">
			<h1>{$LANG.accessdenied}</h1>
		</div>
		<div class="alert alert-danger">
			<h4>{$LANG.bannedyourip} {$ip} {$LANG.bannedhasbeenbanned}</h4>
			<ul>
				<li>{$LANG.bannedbanreason}: {$reason}</li>
				<li>{$LANG.bannedbanexpires}: {$expires}</li>
			</ul>
		</div>
	</div>
</div>