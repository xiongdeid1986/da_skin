{include file="$template/pageheader.tpl" title=$LANG.navopenticket}

<script>
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
</script>

<script>
	{literal}
	$(function () { $("[data-toggle='tooltip']").tooltip(); });
	{/literal}
</script>

	<script>
	{literal}
		$(document)
			.on('change', '.btn-file :file', function() {
				var input = $(this),
				numFiles = input.get(0).files ? input.get(0).files.length : 1,
				label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
				input.trigger('fileselect', [numFiles, label]);
		});
		
		$(document).ready( function() {
			$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
				
				var input = $(this).parents('.input-group').find(':text'),
					log = numFiles > 1 ? numFiles + ' files selected' : label;
				
				if( input.length ) {
					input.val(log);
				} else {
					if( log ) alert(log);
				}
				
			});
		});		
		{/literal}
	</script>

{if $errormessage}
<div class="alert alert-danger">
	<p>{$LANG.clientareaerrors}</p>
	<ul>
		{$errormessage}
	</ul>
</div>
{/if}
<form name="submitticket" method="post" action="{$smarty.server.PHP_SELF}?step=3" enctype="multipart/form-data" class="center95 form-stacked">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>					
						<input type="text" value="{$smarty.now|date_format:'%d/%m/%Y'}" disabled class="form-control">
					</div>
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-map-marker"></span> </span>	
						<select name="deptid" class="form-control" id="deptid">{foreach from=$departments item=department}<option value="{$department.id}"{if $department.id eq $deptid} selected{/if}>{$department.name}</option>{/foreach}</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-flag"></span> </span>	
						<input type="text" value="{$LANG.supportticketsstatusopen}" disabled class="form-control">
					</div>
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-fire"></span> </span>	
						<select name="urgency" class="form-control" id="priority"><option value="High"{if $urgency eq "High"} selected{/if}>{$LANG.supportticketsticketurgencyhigh}</option><option value="Medium"{if $urgency eq "Medium" || !$urgency} selected{/if}>{$LANG.supportticketsticketurgencymedium}</option><option value="Low"{if $urgency eq "Low"} selected{/if}>{$LANG.supportticketsticketurgencylow}</option></select>
					</div></div></div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<label for="name">{$LANG.supportticketsclientname}</label>
							<div class="form-group">
								{if $loggedin}<input type="text" name="name" id="name" value="{$clientname}" disabled class="form-control">{else}<input type="text" name="name" id="name" value="{$name}" class="form-control">{/if}
							</div>    
						</div>         
						<div class="col-md-6">                  
							<label for="email">{$LANG.supportticketsclientemail}</label>
							<div class="form-group"> 
								{if $loggedin}<input type="text" name="email" id="email" value="{$email}" disabled class="form-control">{else}<input type="text" name="email" id="email" value="{$email}" class="form-control">{/if}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<label for="subject">{$LANG.supportticketsticketsubject}</label>
							<div class="form-group">
								<input class="form-control" type="text" name="subject" id="subject" value="{$subject}"/>
							</div>
						</div>
					</div>	
					{if $relatedservices}
					<div class="row">
						<div class="col-lg-12">
							<label for="relatedservice">{$LANG.relatedservice}</label>
							<div class="form-group">
								<select name="relatedservice" class="form-control" id="relatedservice">
									<option value="">{$LANG.none}</option>
									{foreach from=$relatedservices item=relatedservice}
									<option value="{$relatedservice.id}">{$relatedservice.name} ({$relatedservice.status})</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					{/if} 
					<div class="row">
						<div class="col-lg-12">
							<label for="message">{$LANG.contactmessage}</label>
							<div class="form-group">
								<textarea name="message" id="message" rows="5" class="form-control">{$message}</textarea>
							</div>
						</div>
					</div>
					<div id="searchresults" class="contentbox well well-sm" style="display:none;"></div>
					{foreach key=num item=customfield from=$customfields}
					<div class="row">
					<div class="col-lg-12">
					<div class="form-group">
						<label class="control-label bold" for="customfield{$customfield.id}">{$customfield.name} <a href="javascript:void(0)" data-toggle="tooltip" title="{$customfield.description}" data-original-title="{$customfield.description}"><span class="glyphicon glyphicon-question-sign"></span></a></label> 
						{$customfield.input}						
						</div>
					</div>
					</div>
					{/foreach}
					<div id="fileuploads">
					<div class="form-group">
					<label>{$LANG.supportticketsticketattachments}</label> <a href="javascript:void(0)" data-toggle="tooltip" title="{$LANG.supportticketsallowedextensions}: {$allowedfiletypes}" data-original-title="{$LANG.supportticketsallowedextensions}: {$allowedfiletypes}"><span class="glyphicon glyphicon-question-sign"></span></a>
							<div class="input-group input-group-sm">
								<span class="input-group-btn">
									<span class="btn btn-default btn-file">
										<span class="glyphicon glyphicon-folder-open"></span> <input type="file" name="attachments[]" multiple="">
									</span>
								</span>
								<input type="text" class="form-control" readonly="">
							</div>
						</div>
						</div>
						<div class="form-group"><a href="#" class="btn btn-xs btn-default" onclick="extraTicketAttachment();return false"><span class="glyphicon glyphicon-plus-sign"></span> {$LANG.addmore}</a></div>						
						{if $capatacha}
						<h4>{$LANG.captchatitle} <small>{$LANG.captchaverify}</small></h4>
						{if $capatacha eq "recaptcha"}
						<div>{$recapatchahtml}</div>
						{else}
						<div class="row"><div class="col-md-12"><div class="form-group"><img src="includes/verifyimage.php" /></div></div></div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" name="code" class="form-control input-sm" maxlength="5" />
								</div>
							</div>
						</div>
						{/if}
						{/if}
						<div class="row">
							<div class="col-md-12">
								<div class="btn-toolbar pull-right">
								<input class="btn btn-link" type="reset" value="{$LANG.cancel}" />							
								<input class="btn btn-primary" type="submit" name="save" value="{$LANG.supportticketsticketsubmit}" />
							</div>
							</div>
						</div>
					</div>
				</div>
			</form>