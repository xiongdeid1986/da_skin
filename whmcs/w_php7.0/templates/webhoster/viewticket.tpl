{*
 **********************************************************
 * Responsive (WebHoster) WHMCS Theme	
 * Copyright © 2015 ThemeMetro.com, All Rights Reserved
 * Developed by: Team Theme Metro
 * Website: http://www.thememetro.com
 **********************************************************
*}

{if $error}
<p>{$LANG.supportticketinvalid}</p>
{else}
{include file="$template/pageheader.tpl" title=$LANG.supportticketsviewticket|cat:' #'|cat:$tid}

{if $errormessage}
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
    <p class="bold">{$LANG.clientareaerrors}</p>
    <ul>
        {$errormessage}
    </ul>
</div>
{/if}

<h3>{$subject}</h3>

<div class="ticketdetailscontainer">
<div class="row">
    <div class="col-sm-3">
        <div class="tickets-internalpadding">
            {$LANG.supportticketsubmitted}
            <div class="detail">{$date}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="tickets-internalpadding">
            {$LANG.supportticketsdepartment}
            <div class="detail">{$department}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="tickets-internalpadding">
            {$LANG.supportticketsstatus}
            <div class="detail">{$status}</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="tickets-internalpadding">
            {$LANG.supportticketspriority}
            <div class="detail">{$urgency}</div>
        </div>
    </div>
    <div class="clear"></div>
</div>
</div>

{if $customfields}
<table class="table table-striped table-bordered well tc-table">
{foreach from=$customfields item=customfield}
<tr><td>{$customfield.name}:</td><td>{$customfield.value}</td></tr>
{/foreach}
</table>
{/if}

<div class="clearfix btn-group">
	<input type="button" value="{$LANG.clientareabacklink}" class="btn" onclick="window.location='supporttickets.php'" /> <input type="button" value="{$LANG.supportticketsreply}" class="btn btn-success" onclick="jQuery('#replycont').slideToggle()" />{if $showclosebutton} <input type="button" value="{$LANG.supportticketsclose}" class="btn" onclick="window.location='{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;closeticket=true'" />{/if}
</div>

<div id="replycont" class="ticketreplybox{if !$smarty.get.postreply} {/if}" style="display:none">
<form method="post" action="{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;postreply=true" enctype="multipart/form-data" class="form-horizontal">

    <fieldset>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="name">{$LANG.supportticketsclientname}</label>
        	<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-3 disabled" type="text" id="name" value="{$clientname}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-3" type="text" name="replyname" id="name" value="{$replyname}" />{/if}
        	</div>
        </div>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="email">{$LANG.supportticketsclientemail}</label>
        	<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-5 disabled" type="text" id="email" value="{$email}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-5" type="text" name="replyemail" id="email" value="{$replyemail}" />{/if}
        	</div>
        </div>

	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="message">{$LANG.contactmessage}</label>
			<div class="col-sm-9">
			    <textarea name="replymessage" id="message" rows="12" class="col-xs-12 col-sm-6">{$replymessage}</textarea>
			</div>
		</div>

	    <div class="form-group">
			<label class="col-sm-3 control-label"></label>	
			<div class="col-sm-9">
				<p><a onclick="jQuery('#addattachments').slideToggle()" class="btn btn-xs btn-inverse"><i class="fa fa-plus"></i> {$LANG.supportticketsticketattachments}</a></p>
				<div id="addattachments" class="{if !$smarty.get.postreply} {/if}" style="display:none">
					<input type="file" name="attachments[]" style="width:70%;" /><br />
					<div id="fileuploads"></div>
					<a href="#" onclick="extraTicketAttachment();return false"><i class="fa fa-plus"></i> {$LANG.addmore}</a><br />({$LANG.supportticketsallowedextensions}: {$allowedfiletypes})
				</div>
			</div>
		</div>
    </fieldset>
<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<p><input type="submit" value="{$LANG.supportticketsticketsubmit}" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="btn btn-success" /></p>
	</div>
</div>

</form>
</div>

<div class="ticketmsgs">
{foreach from=$descreplies key=num item=reply}
    <div class="{if $reply.admin}admin{else}client{/if}header">
        {if $reply.admin}
            <h5><i class="fa fa-comments"></i> {$reply.name} // {$LANG.supportticketsstaff}</h5>
        {elseif $reply.contactid}
            <h5 class="text-gray"><i class="fa fa-comments"></i> {$reply.name} // {$LANG.supportticketscontact}</h5>
        {elseif $reply.userid}
            <h5 class="text-gray"><i class="fa fa-comments"></i> {$reply.name} // {$LANG.supportticketsclient}</h5>
        {else}
            <h5 class="text-gray"><i class="fa fa-comments"></i> {$reply.name} // {$reply.email}</h5>
        {/if}
    </div>
    <div class="{if $reply.admin}admin{else}client{/if}msg">

        {$reply.message}

        {if $reply.attachments}
        <div class="attachments">
            <h6 class="text-gray">{$LANG.supportticketsticketattachments}:</h6>
            {foreach from=$reply.attachments key=num item=attachment}
            <i class="fa fa-paperclip text-gray"></i> <a href="dl.php?type={if $reply.id}ar&id={$reply.id}{else}a&id={$id}{/if}&i={$num}">{$attachment}</a><br />
            {/foreach}
        </div>
        {/if}

    </div>
	    <div class="ticket-footer clearfix"> 
			<div class="tickets-timestamp"><i class="fa fa-clock-o text-green"></i> {$reply.date}</div>		
{if $reply.id && $reply.admin && $ratingenabled}
        {if $reply.rating}
        <table class="ticketrating">
            <tr>
                <td>{$LANG.ticketreatinggiven}&nbsp;</td>
                {foreach from=$ratings item=rating}
                <td background="images/rating_{if $reply.rating>=$rating}pos{else}neg{/if}.png"></td>
                {/foreach}
            </tr>
        </table>
        {else}
        <table class="ticketrating">
            <tr onmouseout="rating_leave('rate{$reply.id}')">
                <td class="point" onmouseover="rating_hover('rate{$reply.id}_1')" onclick="rating_select('{$tid}','{$c}','rate{$reply.id}_1')"><strong>{$LANG.ticketratingpoor}&nbsp;</strong></td>
                {foreach from=$ratings item=rating}
                <td class="star" id="rate{$reply.id}_{$rating}" onmouseover="rating_hover(this.id)" onclick="rating_select('{$tid}','{$c}',this.id)"></td>
                {/foreach}
                <td class="point" onmouseover="rating_hover('rate{$reply.id}_5')" onclick="rating_select('{$tid}','{$c}','rate{$reply.id}_5')"><strong>&nbsp;{$LANG.ticketratingexcellent}</strong></td>
            </tr>
        </table>
	{/if}
<div class="clear"></div>
{/if}							
		</div>
{/foreach}
</div>

<div class="clearfix btn-group">
	<input type="button" value="{$LANG.clientareabacklink}" class="btn" onclick="window.location='supporttickets.php'" /> <input type="button" value="{$LANG.supportticketsreply}" class="btn btn-success" onclick="jQuery('#replycont2').slideToggle()" />{if $showclosebutton} <input type="button" value="{$LANG.supportticketsclose}" class="btn" onclick="window.location='{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;closeticket=true'" />{/if}
</div>

<div id="replycont2" class="ticketreplybox" style="display:none">
<form method="post" action="{$smarty.server.PHP_SELF}?tid={$tid}&amp;c={$c}&amp;postreply=true" enctype="multipart/form-data" class="form-horizontal">

	<fieldset>
        <div class="form-group">
			<label class="col-sm-3 control-label bold" for="name">{$LANG.supportticketsclientname}</label>
        	<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-3 disabled" type="text" id="name" value="{$clientname}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-3" type="text" name="replyname" id="name" value="{$replyname}" />{/if}
        	</div>
        </div>
        <div class="form-group">
			<label class="col-sm-3 control-label bold" for="email">{$LANG.supportticketsclientemail}</label>
        	<div class="col-sm-9">
				{if $loggedin}<input class="col-xs-12 col-sm-5 disabled" type="text" id="email" value="{$email}" disabled="disabled" />{else}<input class="col-xs-12 col-sm-5" type="text" name="replyemail" id="email" value="{$replyemail}" />{/if}
        	</div>
        </div>

	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="message">{$LANG.contactmessage}</label>
			<div class="col-sm-9">
			    <textarea name="replymessage" id="message" rows="12" class="col-xs-12 col-sm-6">{$replymessage}</textarea>
			</div>
		</div>

	    <div class="form-group">
		    <label class="col-sm-3 control-label"></label>			
			<div class="col-sm-9">
				<p><a onclick="jQuery('#addattachments2').slideToggle()" class="btn btn-xs btn-inverse"><i class="fa fa-plus"></i> {$LANG.supportticketsticketattachments}</a></p>
				<div id="addattachments2" class="{if !$smarty.get.postreply} {/if}" style="display:none">
					<input type="file" name="attachments[]" style="width:70%;" /><br />
					<div id="fileuploads"></div>
					<a href="#" onclick="extraTicketAttachment();return false"><i class="fa fa-plus"></i> {$LANG.addmore}</a><br />({$LANG.supportticketsallowedextensions}: {$allowedfiletypes})
				</div>
			</div>
		</div>
    </fieldset>
<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<p><input type="submit" value="{$LANG.supportticketsticketsubmit}" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="btn btn-success" /></p>
	</div>
</div>

</form>
</div>

{/if}

<div class="space-32"></div>