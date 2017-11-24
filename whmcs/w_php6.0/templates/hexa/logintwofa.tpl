<div class="row">
	<div class="col-lg-12">
		{include file="$template/pageheader.tpl" title=$LANG.twofactorauth}
		{if $newbackupcode}
		<div class="alert alert-success">
			{$LANG.twofabackupcodereset}
		</div>
		{elseif $incorrect}
		<div class="alert alert-danger">
			{$LANG.twofa2ndfactorincorrect}
		</div>
		{elseif $error}
		<div class="alert alert-danger">
			{$error}
		</div>
		{else}
		<div class="alert alert-warning">
			<p>{$LANG.twofa2ndfactorreq}</p>
		</div>
		{/if}
		<form method="post" action="{$systemsslurl}dologin.php" id="frmlogin">
			{if $newbackupcode}
			<input type="hidden" name="newbackupcode" value="1" />
			<h2>{$LANG.twofanewbackupcodeis}</h2>
			<div class="alert alert-warning">
				<p>{$newbackupcode}</p>
			</div>
			<p>{$LANG.twofabackupcodeexpl}</p>
			<input type="submit" value="{$LANG.continue} &raquo;" class="btn btn-default" />
			{elseif $backupcode}
			<input type="hidden" name="backupcode" value="1" />
			<div class="form-group"> 	
				<div class="input-group">
					<input type="text" name="code" class="form-control" />
					<span class="input-group-btn">
						<input type="submit" value="Login &raquo;" class="btn btn-default" />
					</span>
				</div>
			</div>
			{else}
			{$challenge}
			{/if}
			{if !$newbackupcode}
			<div class="alert alert-info">
				{if $backupcode}
				{$LANG.twofabackupcodelogin}
				{else}
				{$LANG.twofacantaccess2ndfactor} <a href="clientarea.php?backupcode=1">{$LANG.twofaloginusingbackupcode}</a></p>
				{/if}
			</div>
			{/if}
		</form>
		<script type="text/javascript">
			$("#frmlogin input:text:visible:first").focus();
		</script>
	</div>
</div>