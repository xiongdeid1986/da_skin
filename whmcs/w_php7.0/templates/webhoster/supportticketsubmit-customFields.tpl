{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{foreach key=num item=customfield from=$customfields}
    <div class="form-group">
        <label class="col-sm-3 control-label" for="customfield{$customfield.id}">{$customfield.name}</label>
        <div class="col-sm-9">
            {$customfield.input} {$customfield.description}
        </div>
    </div>
{/foreach}