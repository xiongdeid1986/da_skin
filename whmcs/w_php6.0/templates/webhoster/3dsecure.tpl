{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<br /><br />

<p class="text-center">{$LANG.creditcard3dsecure}</p>

<br />

<div id="submitfrm" class="text-center">

{$code}

<br /><br />

<iframe name="3dauth" height="500" scrolling="auto" src="about:blank" style="width:80%;border:1px solid #fff;"></iframe>

</div>

<br /><br /><br />

{literal}
<script language="javascript">
setTimeout ( "autoForward()" , 1000 );
function autoForward() {
	var submitForm = $("#submitfrm").find("form");
    submitForm.submit();
}
</script>
{/literal}