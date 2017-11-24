{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

<div class="page-header">
	<h1>{$department}</h1>
</div>

<p><i class="fa fa-ticket text-success"></i> {$LANG.supportticketsintro}</p>
<div class="hr hr-dotted"></div>

<script language="javascript">
var currentcheckcontent,lastcheckcontent;
{if $kbsuggestions}
{literal}
function getticketsuggestions() {
    currentcheckcontent = jQuery("#message").val();
    if (currentcheckcontent!=lastcheckcontent && currentcheckcontent!="") {
        jQuery.post("submitticket.php", { action: "getkbarticles", text: currentcheckcontent },
        function(data){
            if (data) {
                jQuery("#searchresults").html(data);
                jQuery("#searchresults").slideDown();
            }
        });
        lastcheckcontent = currentcheckcontent;
    }
    setTimeout('getticketsuggestions();', 3000);
}
getticketsuggestions();
{/literal}
{/if}
{literal}
$( document ).ready(function() {
    getCustomFields();
});
function getCustomFields() {
    /**
     * Load the custom fields for the specific department
     */
    jQuery("#department").prop('disabled', true);
    jQuery("#customFields").load(
        "submitticket.php",
        { action: "getcustomfields", deptid: jQuery("#department").val() }
    );
    jQuery("#customFields").slideDown();
    jQuery("#department").prop('disabled', false);
}
{/literal}
</script>

{if $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
    <p>{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<form name="submitticket" method="post" action="{$smarty.server.PHP_SELF}?step=3" enctype="multipart/form-data" class="form-horizontal">
	<fieldset>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="name">{$LANG.supportticketsclientname}</label>
        	<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-3 disabled" type="text" id="name" value="{$clientname}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-3" type="text" name="name" id="name" value="{$name}" />{/if}
        	</div>
        </div>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="email">{$LANG.supportticketsclientemail}</label>
			<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-5 disabled" type="text" id="email" value="{$email}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-3" type="text" name="email" id="email" value="{$email}" />{/if}
        	</div>
        </div>
		<div class="hr hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label" for="subject">{$LANG.supportticketsticketsubject}</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-6" type="text" name="subject" id="subject" value="{$subject}" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="name">{$LANG.supportticketsdepartment}</label>
        	<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="deptid" id="department" disabled onchange="getCustomFields()">
                {foreach from=$departments item=department}
                    <option value="{$department.id}"{if $department.id eq $deptid} selected="selected"{/if}>{$department.name}</option>
                {/foreach}
                 </select>
        	</div>
        </div>
   	    <div class="form-group">
			<label class="col-sm-3 control-label" for="priority">{$LANG.supportticketspriority}</label>
        	<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="urgency" id="priority">
                    <option value="High"{if $urgency eq "High"} selected="selected"{/if}>{$LANG.supportticketsticketurgencyhigh}</option>
                    <option value="Medium"{if $urgency eq "Medium" || !$urgency} selected="selected"{/if}>{$LANG.supportticketsticketurgencymedium}</option>
                    <option value="Low"{if $urgency eq "Low"} selected="selected"{/if}>{$LANG.supportticketsticketurgencylow}</option>
                 </select>
        	</div>
        </div>
		{if $relatedservices}
        <div class="form-group">
			<label class="col-sm-3 control-label" for="relatedservice">{$LANG.relatedservice}</label>
			<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="relatedservice" id="relatedservice">
                    <option value="">{$LANG.none}</option>
                    {foreach from=$relatedservices item=relatedservice}
                    <option value="{$relatedservice.id}">{$relatedservice.name} ({$relatedservice.status})</option>
                    {/foreach}
                </select>
        	</div>
        </div>
		{/if}

	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="message">{$LANG.contactmessage}</label>
			<div class="col-sm-9">
			    <textarea name="message" id="message" rows="12" class="col-xs-12 col-sm-6">{$message}</textarea>
			</div>
		</div>
		 <div id="customFields" class="contentbox" style="display:none;"></div>
	    <div class="form-group">
			<label class="col-sm-3 control-label"></label>	
			<div class="col-sm-9">
				<p><a onclick="jQuery('#addattachments').slideToggle()" class="btn btn-xs btn-inverse"><i class="fa fa-plus"></i> {$LANG.supportticketsticketattachments}</p></a>
				<div id="addattachments" class="{if !$smarty.get.postreply}{/if}" style="display: none;">
					<input type="file" name="attachments[]" style="width:70%;" /><br />
					<div id="fileuploads"></div>
					<a href="#" onclick="extraTicketAttachment();return false"><i class="fa fa-plus"></i> {$LANG.addmore}</a><br />({$LANG.supportticketsallowedextensions}: {$allowedfiletypes})
				</div>
			</div>
		</div>
    </fieldset>

<div id="searchresults" style="display:none;"></div>

		{if $capatacha}
		<div class="hr hr32 hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label">{$LANG.captchatitle}</label>
				<div class="col-xs-12 col-sm-6">
					<p>{$LANG.captchaverify}</p>
				{if $capatacha eq "recaptcha"}
					<div align="center">{$recapatchahtml}</div>
				{else}
				<p><img src="includes/verifyimage.php" align="middle" /> <input type="text" name="code" size="10" maxlength="5" class="input-small" /></p>
				{/if}
				</div>
		</div>
		{/if}

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" name="save" value="{$LANG.supportticketsticketsubmit}" />
		<input class="btn" type="reset" value="{$LANG.cancel}" />
	</div>
</div>

</form>