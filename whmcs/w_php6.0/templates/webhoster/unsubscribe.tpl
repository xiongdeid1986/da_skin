{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{include file="$template/pageheader.tpl" title=$LANG.newsletterunsubscribe}

<br /><br /><br />

    {if $successful}
    <div class="alert alert-success">
        <p class="text-center">{$LANG.unsubscribesuccess}</p>
    </div>
    <p class="text-center">{$LANG.newsletterremoved}</p>
    {/if}

    {if $errormessage}
    <div class="alert alert-danger">
        <p class="text-center">{$LANG.erroroccured}</p>
    </div>
    <p class="text-center">{$errormessage}</p>
    {/if}

    <p class="text-center">{$LANG.newsletterresubscribe|sprintf2:'<a href="clientarea.php?action=details">':'</a>'}</p>


<br /><br /><br /><br />