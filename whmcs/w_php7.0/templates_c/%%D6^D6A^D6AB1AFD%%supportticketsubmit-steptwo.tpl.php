<?php /* Smarty version 2.6.28, created on 2017-08-10 16:51:16
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/supportticketsubmit-steptwo.tpl */ ?>

<div class="page-header">
	<h1><?php echo $this->_tpl_vars['department']; ?>
</h1>
</div>

<p><i class="fa fa-ticket text-success"></i> <?php echo $this->_tpl_vars['LANG']['supportticketsintro']; ?>
</p>
<div class="hr hr-dotted"></div>

<script language="javascript">
var currentcheckcontent,lastcheckcontent;
<?php if ($this->_tpl_vars['kbsuggestions']): ?>
<?php echo '
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
    setTimeout(\'getticketsuggestions();\', 3000);
}
getticketsuggestions();
'; ?>

<?php endif; ?>
<?php echo '
$( document ).ready(function() {
    getCustomFields();
});
function getCustomFields() {
    /**
     * Load the custom fields for the specific department
     */
    jQuery("#department").prop(\'disabled\', true);
    jQuery("#customFields").load(
        "submitticket.php",
        { action: "getcustomfields", deptid: jQuery("#department").val() }
    );
    jQuery("#customFields").slideDown();
    jQuery("#department").prop(\'disabled\', false);
}
'; ?>

</script>

<?php if ($this->_tpl_vars['errormessage']): ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
    <p><?php echo $this->_tpl_vars['LANG']['clientareaerrors']; ?>
</p>
    <ul>
        <?php echo $this->_tpl_vars['errormessage']; ?>

    </ul>
</div>
<?php endif; ?>

<form name="submitticket" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?step=3" enctype="multipart/form-data" class="form-horizontal">
	<fieldset>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="name"><?php echo $this->_tpl_vars['LANG']['supportticketsclientname']; ?>
</label>
        	<div class="col-sm-9">
				<?php if ($this->_tpl_vars['loggedin']): ?><input class="col-xs-12 col-sm-3 disabled" type="text" id="name" value="<?php echo $this->_tpl_vars['clientname']; ?>
" disabled="disabled" /><?php else: ?><input class="col-xs-12 col-sm-3" type="text" name="name" id="name" value="<?php echo $this->_tpl_vars['name']; ?>
" /><?php endif; ?>
        	</div>
        </div>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="email"><?php echo $this->_tpl_vars['LANG']['supportticketsclientemail']; ?>
</label>
			<div class="col-sm-9">
				<?php if ($this->_tpl_vars['loggedin']): ?><input class="col-xs-12 col-sm-5 disabled" type="text" id="email" value="<?php echo $this->_tpl_vars['email']; ?>
" disabled="disabled" /><?php else: ?><input class="col-xs-12 col-sm-3" type="text" name="email" id="email" value="<?php echo $this->_tpl_vars['email']; ?>
" /><?php endif; ?>
        	</div>
        </div>
		<div class="hr hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label" for="subject"><?php echo $this->_tpl_vars['LANG']['supportticketsticketsubject']; ?>
</label>
			<div class="col-sm-9">
				<input class="col-xs-12 col-sm-6" type="text" name="subject" id="subject" value="<?php echo $this->_tpl_vars['subject']; ?>
" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="name"><?php echo $this->_tpl_vars['LANG']['supportticketsdepartment']; ?>
</label>
        	<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="deptid" id="department" disabled onchange="getCustomFields()">
                <?php $_from = $this->_tpl_vars['departments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['department']):
?>
                    <option value="<?php echo $this->_tpl_vars['department']['id']; ?>
"<?php if ($this->_tpl_vars['department']['id'] == $this->_tpl_vars['deptid']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['department']['name']; ?>
</option>
                <?php endforeach; endif; unset($_from); ?>
                 </select>
        	</div>
        </div>
   	    <div class="form-group">
			<label class="col-sm-3 control-label" for="priority"><?php echo $this->_tpl_vars['LANG']['supportticketspriority']; ?>
</label>
        	<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="urgency" id="priority">
                    <option value="High"<?php if ($this->_tpl_vars['urgency'] == 'High'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['LANG']['supportticketsticketurgencyhigh']; ?>
</option>
                    <option value="Medium"<?php if ($this->_tpl_vars['urgency'] == 'Medium' || ! $this->_tpl_vars['urgency']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['LANG']['supportticketsticketurgencymedium']; ?>
</option>
                    <option value="Low"<?php if ($this->_tpl_vars['urgency'] == 'Low'): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['LANG']['supportticketsticketurgencylow']; ?>
</option>
                 </select>
        	</div>
        </div>
		<?php if ($this->_tpl_vars['relatedservices']): ?>
        <div class="form-group">
			<label class="col-sm-3 control-label" for="relatedservice"><?php echo $this->_tpl_vars['LANG']['relatedservice']; ?>
</label>
			<div class="col-sm-9">
				<select class="col-xs-12 col-sm-3" name="relatedservice" id="relatedservice">
                    <option value=""><?php echo $this->_tpl_vars['LANG']['none']; ?>
</option>
                    <?php $_from = $this->_tpl_vars['relatedservices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['relatedservice']):
?>
                    <option value="<?php echo $this->_tpl_vars['relatedservice']['id']; ?>
"><?php echo $this->_tpl_vars['relatedservice']['name']; ?>
 (<?php echo $this->_tpl_vars['relatedservice']['status']; ?>
)</option>
                    <?php endforeach; endif; unset($_from); ?>
                </select>
        	</div>
        </div>
		<?php endif; ?>

	    <div class="form-group">
		    <label class="col-sm-3 control-label" for="message"><?php echo $this->_tpl_vars['LANG']['contactmessage']; ?>
</label>
			<div class="col-sm-9">
			    <textarea name="message" id="message" rows="12" class="col-xs-12 col-sm-6"><?php echo $this->_tpl_vars['message']; ?>
</textarea>
			</div>
		</div>
		 <div id="customFields" class="contentbox" style="display:none;"></div>
	    <div class="form-group">
			<label class="col-sm-3 control-label"></label>	
			<div class="col-sm-9">
				<p><a onclick="jQuery('#addattachments').slideToggle()" class="btn btn-xs btn-inverse"><i class="fa fa-plus"></i> <?php echo $this->_tpl_vars['LANG']['supportticketsticketattachments']; ?>
</p></a>
				<div id="addattachments" class="<?php if (! $_GET['postreply']): ?><?php endif; ?>" style="display: none;">
					<input type="file" name="attachments[]" style="width:70%;" /><br />
					<div id="fileuploads"></div>
					<a href="#" onclick="extraTicketAttachment();return false"><i class="fa fa-plus"></i> <?php echo $this->_tpl_vars['LANG']['addmore']; ?>
</a><br />(<?php echo $this->_tpl_vars['LANG']['supportticketsallowedextensions']; ?>
: <?php echo $this->_tpl_vars['allowedfiletypes']; ?>
)
				</div>
			</div>
		</div>
    </fieldset>

<div id="searchresults" style="display:none;"></div>

		<?php if ($this->_tpl_vars['capatacha']): ?>
		<div class="hr hr32 hr-dotted"></div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label"><?php echo $this->_tpl_vars['LANG']['captchatitle']; ?>
</label>
				<div class="col-xs-12 col-sm-6">
					<p><?php echo $this->_tpl_vars['LANG']['captchaverify']; ?>
</p>
				<?php if ($this->_tpl_vars['capatacha'] == 'recaptcha'): ?>
					<div align="center"><?php echo $this->_tpl_vars['recapatchahtml']; ?>
</div>
				<?php else: ?>
				<p><img src="includes/verifyimage.php" align="middle" /> <input type="text" name="code" size="10" maxlength="5" class="input-small" /></p>
				<?php endif; ?>
				</div>
		</div>
		<?php endif; ?>

<div class="clearfix form-actions">
	<div class="col-md-offset-3 col-md-9">
		<input class="btn btn-success" type="submit" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" name="save" value="<?php echo $this->_tpl_vars['LANG']['supportticketsticketsubmit']; ?>
" />
		<input class="btn" type="reset" value="<?php echo $this->_tpl_vars['LANG']['cancel']; ?>
" />
	</div>
</div>

</form>