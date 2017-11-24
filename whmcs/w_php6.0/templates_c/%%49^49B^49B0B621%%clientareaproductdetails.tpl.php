<?php /* Smarty version 2.6.28, created on 2016-12-13 23:53:47
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareaproductdetails.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareaproductdetails.tpl', 69, false),)), $this); ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['product'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['modulechangepwresult'] == 'success'): ?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	<?php echo $this->_tpl_vars['LANG']['serverchangepasswordsuccessful']; ?>

</div>
<?php elseif ($this->_tpl_vars['modulechangepwresult'] == 'error'): ?>
<div class="alert alert-danger>
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	<?php echo $this->_tpl_vars['modulechangepasswordmessage']; ?>

</div>
<?php elseif ($this->_tpl_vars['modulecustombuttonresult'] == 'success'): ?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	<?php echo $this->_tpl_vars['LANG']['moduleactionsuccess']; ?>

</div>
<?php elseif ($this->_tpl_vars['modulecustombuttonresult']): ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>
	<strong><?php echo $this->_tpl_vars['LANG']['moduleactionfailed']; ?>
:</strong> <?php echo $this->_tpl_vars['modulecustombuttonresult']; ?>

</div>
<?php endif; ?>

<div class="tc-tabsbar arrow" id="tabs-1">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-information" data-toggle="tab" title="<?php echo $this->_tpl_vars['LANG']['information']; ?>
"><i class="icon-info-sign"></i> <?php echo $this->_tpl_vars['LANG']['information']; ?>
</a></li>
		<?php if ($this->_tpl_vars['modulechangepassword']): ?><li><a href="#tab-changepw" data-toggle="tab" title="<?php echo $this->_tpl_vars['LANG']['serverchangepassword']; ?>
"><i class="icon-key"></i> <?php echo $this->_tpl_vars['LANG']['serverchangepassword']; ?>
</a></li><?php endif; ?>
		<?php if ($this->_tpl_vars['downloads']): ?><li><a href="#tab-downloads" data-toggle="tab" title="<?php echo $this->_tpl_vars['LANG']['downloadstitle']; ?>
"><?php echo $this->_tpl_vars['LANG']['downloadstitle']; ?>
</a></li><?php endif; ?>
		<?php if ($this->_tpl_vars['addons'] || $this->_tpl_vars['addonsavailable']): ?><li><a href="#tab-addons" data-toggle="tab" title="<?php echo $this->_tpl_vars['LANG']['clientareahostingaddons']; ?>
"><i class="icon-plus"></i> <?php echo $this->_tpl_vars['LANG']['clientareahostingaddons']; ?>
</a></li><?php endif; ?>
	</ul>

<div class="tab-content">
	<div class="tab-pane active" id="tab-information">
		<div class="row">
			<div class="col-sm-4">
				<h2><?php echo $this->_tpl_vars['LANG']['information']; ?>
</h2>
				<?php if ($this->_tpl_vars['groupname']): ?><?php echo $this->_tpl_vars['groupname']; ?>
 - <?php endif; ?><?php echo $this->_tpl_vars['product']; ?>
 <span class="label label-<?php echo $this->_tpl_vars['rawstatus']; ?>
 arrowed-right"><?php echo $this->_tpl_vars['status']; ?>
</span><?php if ($this->_tpl_vars['domain']): ?><br /><span class="text-primary"><?php echo $this->_tpl_vars['domain']; ?>
</span><?php endif; ?><br /><br />
				<p><?php echo $this->_tpl_vars['LANG']['clientareaproductdetailsintro']; ?>
</p>
				<?php if ($this->_tpl_vars['suspendreason']): ?><span class="text-warning"><strong><?php echo $this->_tpl_vars['LANG']['suspendreason']; ?>
</strong>: <?php echo $this->_tpl_vars['suspendreason']; ?>
</span><?php endif; ?>
				
				<div class="btn-toolbar">
					<?php if ($this->_tpl_vars['packagesupgrade'] || $this->_tpl_vars['configoptionsupgrade'] || $this->_tpl_vars['showcancelbutton'] || $this->_tpl_vars['modulecustombuttons']): ?>
						<div class="btn-group">
							<a data-toggle="dropdown" class="btn btn-sm btn-primary dropdown-toggle"><?php echo $this->_tpl_vars['LANG']['productmanagementactions']; ?>
 <i class="fa fa-angle-down"></i></a>
							<ul class="dropdown-menu dropdown-primary">
								<?php $_from = $this->_tpl_vars['modulecustombuttons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['label'] => $this->_tpl_vars['command']):
?>
								<li><a href="clientarea.php?action=productdetails&amp;id=<?php echo $this->_tpl_vars['id']; ?>
&amp;modop=custom&amp;a=<?php echo $this->_tpl_vars['command']; ?>
"><?php echo $this->_tpl_vars['label']; ?>
</a></li>
								<?php endforeach; endif; unset($_from); ?>
								<?php if ($this->_tpl_vars['packagesupgrade']): ?>
								<li><a href="upgrade.php?type=package&amp;id=<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['LANG']['upgradedowngradepackage']; ?>
</a></li>
								<?php endif; ?>
								<?php if ($this->_tpl_vars['configoptionsupgrade']): ?>
								<li><a href="upgrade.php?type=configoptions&amp;id=<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['LANG']['upgradedowngradeconfigoptions']; ?>
</a></li>
								<?php endif; ?>
								<?php if ($this->_tpl_vars['showcancelbutton']): ?>
								<li><a href="clientarea.php?action=cancel&amp;id=<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['LANG']['clientareacancelrequestbutton']; ?>
</a></li>
								<?php endif; ?>
								<li class="divider"></li>
								<li><a href="clientarea.php?action=products"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['backtoserviceslist'])) ? $this->_run_mod_handler('replace', true, $_tmp, '&laquo; ', '') : smarty_modifier_replace($_tmp, '&laquo; ', '')); ?>
</a>
							</ul>
						</div>
					<?php else: ?>
					<?php if ($this->_tpl_vars['clientareaaction'] == 'productdetails'): ?>
							<a href="clientarea.php?action=products" class="btn btn-sm btn-info"><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['backtoserviceslist'])) ? $this->_run_mod_handler('replace', true, $_tmp, '&laquo; ', '') : smarty_modifier_replace($_tmp, '&laquo; ', '')); ?>
</a>
					<?php else: ?>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
?action=productdetails" style="margin-bottom:0"><input type="hidden" name="id" value="<?php echo $this->_tpl_vars['id']; ?>
"><input type="submit" value="<?php echo $this->_tpl_vars['LANG']['clientareabacklink']; ?>
" class="btn btn-sm btn-info"></form>
					<?php endif; ?>
					<?php endif; ?><br /></br /></br />
				</div>
			</div>
			<div class="col-sm-8">
				<div class="portlet">
					<div class="portlet-heading dark">
						<div class="portlet-title">
							<h4><i class="fa fa-th"></i> <?php echo $this->_tpl_vars['LANG']['orderproduct']; ?>
</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box1"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box1" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<thead>
							</thead>
								<?php if ($this->_tpl_vars['dedicatedip']): ?>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['domainregisternsip']; ?>
</td>
									<td><?php echo $this->_tpl_vars['dedicatedip']; ?>
</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['firstpaymentamount']; ?>
</td>
									<td><?php echo $this->_tpl_vars['firstpaymentamount']; ?>
</td>								
								</tr>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['clientareahostingregdate']; ?>
</td>
									<td><?php echo $this->_tpl_vars['regdate']; ?>
</td>
								</tr>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['recurringamount']; ?>
</td>
									<td><?php echo $this->_tpl_vars['recurringamount']; ?>
</td>
								</tr>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
</td>
									<td><?php echo $this->_tpl_vars['nextduedate']; ?>
</td>
								</tr>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['orderbillingcycle']; ?>
</td>
									<td><?php echo $this->_tpl_vars['billingcycle']; ?>
</td>
								</tr>
								<tr>
									<td><?php echo $this->_tpl_vars['LANG']['orderpaymentmethod']; ?>
</td>
									<td><?php echo $this->_tpl_vars['paymentmethod']; ?>
</td>
								</tr>
						</table>
					</div>
					</div>
				</div>
				<?php if ($this->_tpl_vars['username']): ?>
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4><i class="fa fa-key"></i> <?php echo $this->_tpl_vars['LANG']['orderlogininfo']; ?>
</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box2"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box2" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<tr>
								<td><?php echo $this->_tpl_vars['LANG']['serverusername']; ?>
 <i class="fa fa-angle-right text-blue"></i> <?php echo $this->_tpl_vars['username']; ?>
</td>
								<td><?php if ($this->_tpl_vars['password']): ?><?php echo $this->_tpl_vars['LANG']['serverpassword']; ?>
 <i class="fa fa-angle-right text-blue"></i> <?php echo $this->_tpl_vars['password']; ?>
<?php endif; ?></td>
							</tr>
						</table>
					</div>
					</div>
				</div>
				<?php endif; ?>								
				<?php if ($this->_tpl_vars['lastupdate']): ?>
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4></h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box3"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box3" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
							<tr>
								<td><p><?php echo $this->_tpl_vars['LANG']['clientareadiskusage']; ?>
</p><?php echo $this->_tpl_vars['diskusage']; ?>
MB / <?php echo $this->_tpl_vars['disklimit']; ?>
MB (<?php echo $this->_tpl_vars['diskpercent']; ?>
)<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all progress progress-striped active"><span class="ui-progressbar-value ui-widget-header ui-corner-left progress-bar progress-bar-success" style="width:<?php echo $this->_tpl_vars['diskpercent']; ?>
"></span></div></td>
								<td><p><?php echo $this->_tpl_vars['LANG']['clientareabwusage']; ?>
</p><?php echo $this->_tpl_vars['bwusage']; ?>
MB / <?php echo $this->_tpl_vars['bwlimit']; ?>
MB (<?php echo $this->_tpl_vars['bwpercent']; ?>
)<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all progress progress-striped active"><span class="ui-progressbar-value ui-widget-header ui-corner-left progress-bar progress-bar-success" style="width:<?php echo $this->_tpl_vars['bwpercent']; ?>
"></span></div></td>
							</tr>
						</table>
					</div>
					</div>
				</div>
				<?php endif; ?>					
				<div class="portlet">
					<div class="portlet-heading">
						<div class="portlet-title">
							<h4><i class="fa fa-th"></i> <?php echo $this->_tpl_vars['LANG']['cartconfigurationoptions']; ?>
</h4>
						</div>
						<div class="portlet-widgets">
							<a data-toggle="collapse" data-parent="#accordion" href="#pd-box4"><i class="fa fa-chevron-down"></i></a>
						</div>
						<div class="clearfix"></div>
					</div>
					<div id="pd-box4" class="panel-collapse collapse in">
					<div class="portlet-body no-padding">
						<table class="table table-bordered table-hover tc-table">
						<?php $_from = $this->_tpl_vars['configurableoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['configoption']):
?>
							<tr>
								<td><?php echo $this->_tpl_vars['configoption']['optionname']; ?>
</td>	
								<td><?php if ($this->_tpl_vars['configoption']['optiontype'] == 3): ?><?php if ($this->_tpl_vars['configoption']['selectedqty']): ?><?php echo $this->_tpl_vars['LANG']['yes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['no']; ?>
<?php endif; ?><?php elseif ($this->_tpl_vars['configoption']['optiontype'] == 4): ?><?php echo $this->_tpl_vars['configoption']['selectedqty']; ?>
 x <?php echo $this->_tpl_vars['configoption']['selectedoption']; ?>
<?php else: ?><?php echo $this->_tpl_vars['configoption']['selectedoption']; ?>
<?php endif; ?></td>
							</tr>
						<?php endforeach; endif; unset($_from); ?>
						<?php $_from = $this->_tpl_vars['productcustomfields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['customfield']):
?>
							<tr>
								<td><?php echo $this->_tpl_vars['customfield']['name']; ?>
 - (<?php echo $this->_tpl_vars['customfield']['description']; ?>
)</td>		
								<td><?php echo $this->_tpl_vars['customfield']['value']; ?>
</td>
							</tr>
						<?php endforeach; endif; unset($_from); ?>
						</table>
					</div>
					</div>
				</div>				
			</div>
		</div>
		<?php if ($this->_tpl_vars['moduleclientarea']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['moduleclientarea'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'modulebutton', 'btn btn-info btn-sm') : smarty_modifier_replace($_tmp, 'modulebutton', 'btn btn-info btn-sm')); ?>
<?php endif; ?>
	</div>

		
	<div class="tab-pane" id="tab-changepw">		
		<h2><?php echo $this->_tpl_vars['LANG']['serverchangepassword']; ?>
</h2><div class="alert alert-info"><?php echo $this->_tpl_vars['LANG']['serverchangepasswordintro']; ?>
</div>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>
" class="form-horizontal">
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['id']; ?>
">
		<input type="hidden" name="modulechangepassword" value="true">
			<fieldset>
				<input type="hidden" name="action" value="productdetails">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="password"><?php echo $this->_tpl_vars['LANG']['newpassword']; ?>
</label>
						<div class="col-sm-9">
							<input class="col-xs-10 col-sm-5" type="password" name="newpw" id="password">							
						</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="confirmpw"><?php echo $this->_tpl_vars['LANG']['confirmnewpassword']; ?>
</label>
						<div class="col-sm-9">
							<input class="col-xs-10 col-sm-5" type="password" name="confirmpw" id="confirmpw" class="">
						</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="passstrength"><?php echo $this->_tpl_vars['LANG']['pwstrength']; ?>
</label>
							<div class="col-sm-9">
								<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pwstrength.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
							</div>
				</div>
				<div class="clearfix form-actions">
					<div class="col-md-offset-3 col-md-9">
						<button class="btn btn-info btn-sm"><?php echo $this->_tpl_vars['LANG']['clientareasavechanges']; ?>
</button>
						<button class="btn btn-danger btn-sm" type="reset"><?php echo $this->_tpl_vars['LANG']['cancel']; ?>
</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>

	<div class="tab-pane" id="tab-downloads">
		<div class="row">
			<div class="col-sm-4">
				<h2><?php echo $this->_tpl_vars['LANG']['downloadstitle']; ?>
</h2>
				<p>There are the following downloads associated with this product</p><br /></br />
			</div>
			<div class="col-sm-8">
				<?php $_from = $this->_tpl_vars['downloads']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['download']):
?>
						<h4><?php echo $this->_tpl_vars['download']['title']; ?>
 - <a href="<?php echo $this->_tpl_vars['download']['link']; ?>
" title="<?php echo $this->_tpl_vars['LANG']['downloadname']; ?>
 <?php echo $this->_tpl_vars['download']['title']; ?>
" class="btn btn-xs btn-inverse"><i class="fa fa-download"></i> <?php echo $this->_tpl_vars['LANG']['downloadname']; ?>
</a></h4>
						<p><?php echo $this->_tpl_vars['download']['description']; ?>
</p>
				<?php endforeach; endif; unset($_from); ?>
			</div>
		</div>
	</div>

	<div class="tab-pane" id="tab-addons">
			<h2><?php echo $this->_tpl_vars['LANG']['clientareahostingaddons']; ?>
</h2>
			<p><?php echo $this->_tpl_vars['LANG']['yourclientareahostingaddons']; ?>
</p>
		<table class="table table-striped table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th><?php echo $this->_tpl_vars['LANG']['clientareaaddon']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['clientareaaddonpricing']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
</th>
				</tr>
			</thead>
			<?php $_from = $this->_tpl_vars['addons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['addon']):
?>
				<tr>
					<td><?php echo $this->_tpl_vars['addon']['name']; ?>
 &nbsp; <span class="label <?php echo $this->_tpl_vars['addon']['rawstatus']; ?>
"><?php echo $this->_tpl_vars['addon']['status']; ?>
</span>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><i class="fa fa-angle-right bigger-110"></i> <?php echo $this->_tpl_vars['LANG']['clientareaaddonpricing']; ?>
 : <?php echo $this->_tpl_vars['addon']['pricing']; ?>

							<li><i class="fa fa-angle-right bigger-110"></i> <?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
 : <?php echo $this->_tpl_vars['addon']['nextduedate']; ?>
</li>
						</ul>							
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['addon']['pricing']; ?>
</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['addon']['nextduedate']; ?>
</td>
				</tr>
			<?php endforeach; else: ?>
				<tr>
					<td class="text-center" colspan="3"><?php echo $this->_tpl_vars['LANG']['clientareanoaddons']; ?>
</td>
				</tr>
			<?php endif; unset($_from); ?>
		</table>
		
		<?php if ($this->_tpl_vars['addonsavailable']): ?><p><a class="btn btn-success btn-sm" href="cart.php?gid=addons&amp;pid=<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['LANG']['orderavailableaddons']; ?>
</a></p><?php endif; ?>
	</div>	

</div>
</div>