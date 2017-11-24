{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{$LANG.clientareaemails} - {$companyname}</title>

	<link href="templates/{$template}/assets/css/bootstrap.min.css" rel="stylesheet" />

  </head>

  <body class="popupwindow" style="padding: 15px; background: #F5F5F5;">
	
	<div style="padding: 15px; background: #FFFFFF;">
		<h4>{$subject}</h4>
		<div class="popupcontainer">{$message}</div>
		<p class="text-center"><input type="button" value="{$LANG.closewindow}" class="btn btn-danger btn-xs" onclick="window.close()" /></p>
	</div>

  </body>
</html>