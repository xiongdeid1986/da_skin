<?php /* Smarty version 2.6.28, created on 2016-12-15 19:44:21
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/domainchecker.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strtoupper', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/domainchecker.tpl', 64, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['domaintitle'],'desc' => $this->_tpl_vars['LANG']['domaincheckerintro'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['inccode']): ?>
<div class="alert alert-danger">
	<button class="close" data-dismiss="alert">&times</button>
	<?php echo $this->_tpl_vars['LANG']['captchaverifyincorrect']; ?>

</div>
<?php endif; ?>

<div class="tc-tabsbar center-tabs arrow">
	<ul class="nav nav-tabs">
		<li class="active"><a href="domainchecker.php"><?php echo $this->_tpl_vars['LANG']['domainsimplesearch']; ?>
</a></li>
		<?php if ($this->_tpl_vars['bulkdomainsearchenabled']): ?>
		<li><a href="domainchecker.php?search=bulkregister"><?php echo $this->_tpl_vars['LANG']['domainbulksearch']; ?>
</a></li>
		<?php if ($this->_tpl_vars['condlinks']['domaintrans']): ?><li><a href="domainchecker.php?search=bulktransfer"><?php echo $this->_tpl_vars['LANG']['domainbulktransfersearch']; ?>
</a></li><?php endif; ?>
		<?php endif; ?>
	</ul>
</div>

<div class="tab-content padding-16 text-center">
	<form method="post" action="domainchecker.php" class="form-horizontal">
		<p><?php echo $this->_tpl_vars['LANG']['domaincheckerenterdomain']; ?>
</p>
			<input class="input-lg input-xxlarge" name="domain" type="text" value="<?php if ($this->_tpl_vars['domain']): ?><?php echo $this->_tpl_vars['domain']; ?>
<?php endif; ?>" placeholder="<?php echo $this->_tpl_vars['LANG']['domaincheckerdomainexample']; ?>
">
			<div><br /><button class="btn btn-xs btn-warning" type="button" onclick="jQuery('#mtlds').slideToggle();"><i class="fa fa-plus"></i> <?php echo $this->_tpl_vars['LANG']['searchmultipletlds']; ?>
</button><br /><br /></div>
		<div id="mtlds" style="display:none">
		<?php $_from = $this->_tpl_vars['tldslist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['listtld']):
?>
			<label class="checkbox-inline"><input type="checkbox" name="tlds[]" value="<?php echo $this->_tpl_vars['listtld']; ?>
"<?php if (in_array ( $this->_tpl_vars['listtld'] , $this->_tpl_vars['tlds'] ) || ! $this->_tpl_vars['tlds'] && $this->_tpl_vars['num'] == 1): ?> checked="checked"<?php endif; ?>> <?php echo $this->_tpl_vars['listtld']; ?>
</label>
		<?php endforeach; endif; unset($_from); ?>
			<hr>
		</div>
	<?php if ($this->_tpl_vars['capatacha']): ?>
		<p><i class="fa fa-info-circle text-info"></i> <?php echo $this->_tpl_vars['LANG']['captchaverify']; ?>
</p>
		
		<?php if ($this->_tpl_vars['capatacha'] == 'recaptcha'): ?>
			<p><?php echo $this->_tpl_vars['recapatchahtml']; ?>
</p>
		<?php else: ?>
			<img src="includes/verifyimage.php" alt="captcha"> <input type="text" name="code" class="input-sm" style="margin-bottom:0" maxlength="5"><br /><br />
		<?php endif; ?>
	<?php endif; ?>
	<div class="space-6"></div>
	<div class="text-center">
			<button type="submit" value="<?php echo $this->_tpl_vars['LANG']['checkavailability']; ?>
" class="btn btn-success" onclick="$('#modalpleasewait').modal();"><i class="fa fa-search"></i> <?php echo $this->_tpl_vars['LANG']['checkavailability']; ?>
</button>
			<?php if ($this->_tpl_vars['condlinks']['domaintrans']): ?><button type="submit" name="transfer" value="<?php echo $this->_tpl_vars['LANG']['domainstransfer']; ?>
" class="btn btn-inverse" /><?php echo $this->_tpl_vars['LANG']['domainstransfer']; ?>
</button><?php endif; ?>
	</div>
	</form>
</div>


<?php if ($this->_tpl_vars['lookup']): ?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1">
	<?php if ($this->_tpl_vars['available']): ?>
		<div class="alert alert-success text-center"><?php echo $this->_tpl_vars['LANG']['domainavailable1']; ?>
 <?php echo $this->_tpl_vars['sld']; ?>
<?php echo $this->_tpl_vars['ext']; ?>
 <?php echo $this->_tpl_vars['LANG']['domainavailable2']; ?>
</div>
	<?php elseif ($this->_tpl_vars['invalidtld']): ?>
		<div class="alert alert-danger text-center">
			<p><?php echo ((is_array($_tmp=$this->_tpl_vars['invalidtld'])) ? $this->_run_mod_handler('strtoupper', true, $_tmp) : strtoupper($_tmp)); ?>
 <?php echo $this->_tpl_vars['LANG']['domaincheckerinvalidtld']; ?>
</p>
		</div>
	<?php elseif ($this->_tpl_vars['invalid']): ?>
		<div class="alert alert-danger text-center">
			<p><?php echo $this->_tpl_vars['LANG']['ordererrordomaininvalid']; ?>
</p>
		</div>
	<?php elseif ($this->_tpl_vars['error']): ?>
		<div class="alert alert-danger text-center">
			<p><?php echo $this->_tpl_vars['LANG']['domainerror']; ?>
</p>
		</div>
	<?php else: ?>
		<div class="alert alert-danger text-center">
			<p><?php echo $this->_tpl_vars['LANG']['domainunavailable1']; ?>
 <?php echo $this->_tpl_vars['sld']; ?>
<?php echo $this->_tpl_vars['ext']; ?>
 <?php echo $this->_tpl_vars['LANG']['domainunavailable2']; ?>
</p>
		</div>
	<?php endif; ?>

	<?php if (! $this->_tpl_vars['invalid']): ?>
		<form method="post" action="<?php echo $this->_tpl_vars['systemsslurl']; ?>
cart.php?a=add&domain=register" class="form-horizontal">
			<table class="table table-bordered table-hover tc-table">
				<?php $_from = $this->_tpl_vars['availabilityresults']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['result']):
?>
					<tr>
						<td class="col-small center">
						<?php if ($this->_tpl_vars['result']['status'] == 'available'): ?>
							<input type="checkbox" name="domains[]" value="<?php echo $this->_tpl_vars['result']['domain']; ?>
" <?php if ($this->_tpl_vars['num'] == '0' && $this->_tpl_vars['available']): ?>checked <?php endif; ?>/>
							<input type="hidden" name="domainsregperiod[<?php echo $this->_tpl_vars['result']['domain']; ?>
]" value="<?php echo $this->_tpl_vars['result']['period']; ?>
" />
						<?php else: ?>
							<input type="checkbox" disabled>
						<?php endif; ?>
						</td>
						<td><?php echo $this->_tpl_vars['result']['domain']; ?>

							
							<?php if ($this->_tpl_vars['result']['status'] == 'available'): ?>
								<div class="space-4 visible-xs"></div>
                                <ul class="list-unstyled visible-xs">
									<li><select name="domainsregperiod[<?php echo $this->_tpl_vars['result']['domain']; ?>
]"><?php $_from = $this->_tpl_vars['result']['regoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['period'] => $this->_tpl_vars['regoption']):
?><option value="<?php echo $this->_tpl_vars['period']; ?>
"><?php echo $this->_tpl_vars['period']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
 @ <?php echo $this->_tpl_vars['regoption']['register']; ?>
</option><?php endforeach; endif; unset($_from); ?></select></li>
								</ul><?php endif; ?>
								<?php if ($this->_tpl_vars['result']['status'] == 'unavailable'): ?>
								<div class="space-4 visible-xs"></div>
								<ul class="list-unstyled visible-xs">
									<li><a href="#" onclick="popupWindow('whois.php?domain=<?php echo $this->_tpl_vars['result']['domain']; ?>
','whois',650,420);return false">Whois Lookup</a></li>
								</ul>
							<?php endif; ?>
						
						
						</td>
						<td class="col-small center">
						<?php if ($this->_tpl_vars['result']['status'] == 'available'): ?>
							<i class="fa fa-check text-success bigger-110"></i>
						<?php else: ?>
							<i class="fa fa-times text-danger bigger-110"></i>
						<?php endif; ?>
						</td>
						<td class="text-center hidden-xs">
						<?php if ($this->_tpl_vars['result']['status'] == 'unavailable'): ?> 
							<a href="#" onclick="popupWindow('whois.php?domain=<?php echo $this->_tpl_vars['result']['domain']; ?>
','whois',650,420);return false">Whois Lookup</a>
						<?php else: ?>
							<select name="domainsregperiod[<?php echo $this->_tpl_vars['result']['domain']; ?>
]">
							<?php $_from = $this->_tpl_vars['result']['regoptions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['period'] => $this->_tpl_vars['regoption']):
?>
								<option value="<?php echo $this->_tpl_vars['period']; ?>
"><?php echo $this->_tpl_vars['period']; ?>
 <?php echo $this->_tpl_vars['LANG']['orderyears']; ?>
 @ <?php echo $this->_tpl_vars['regoption']['register']; ?>
</option>
							<?php endforeach; endif; unset($_from); ?>
							</select>
						<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; endif; unset($_from); ?>
			</table>
			
			<div class="padding-all text-center">
				<input type="submit" value="<?php echo $this->_tpl_vars['LANG']['ordernowbutton']; ?>
 &raquo;" class="btn btn-success">
			</div>
			
			<div class="space-16"></div>
			
		</form>
	<?php endif; ?>
	</div>
</div>

<?php else: ?>

<div class="portlet">
	<div class="portlet-heading inverse">
		<div class="portlet-title">
			<h4><i class="fa fa-tags"></i> <?php echo $this->_tpl_vars['LANG']['domainspricing']; ?>
</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#domain-price"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="domain-price" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th><?php echo $this->_tpl_vars['LANG']['domaintld']; ?>
</th>
					<th><?php echo $this->_tpl_vars['LANG']['domainminyears']; ?>
</th>
					<th><?php echo $this->_tpl_vars['LANG']['domainsregister']; ?>
</th>
					<th><?php echo $this->_tpl_vars['LANG']['domainstransfer']; ?>
</th>
					<th><?php echo $this->_tpl_vars['LANG']['domainsrenew']; ?>
</td>
				</tr>
			</thead>
	<?php $_from = $this->_tpl_vars['tldpricelist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['tldpricelist']):
?>
				<tr>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['domaintld']; ?>
"><?php echo $this->_tpl_vars['tldpricelist']['tld']; ?>
</td>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['domainminyears']; ?>
"><?php echo $this->_tpl_vars['tldpricelist']['period']; ?>
</td>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['domainsregister']; ?>
"><?php if ($this->_tpl_vars['tldpricelist']['register']): ?><?php echo $this->_tpl_vars['tldpricelist']['register']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['domainregnotavailable']; ?>
<?php endif; ?></td>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['domainstransfer']; ?>
"><?php if ($this->_tpl_vars['tldpricelist']['transfer']): ?><?php echo $this->_tpl_vars['tldpricelist']['transfer']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['domainregnotavailable']; ?>
<?php endif; ?></td>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['domainsrenew']; ?>
"><?php if ($this->_tpl_vars['tldpricelist']['renew']): ?><?php echo $this->_tpl_vars['tldpricelist']['renew']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['domainregnotavailable']; ?>
<?php endif; ?></td>
				</tr>
	<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
	</div>
</div>

	<?php if (! $this->_tpl_vars['loggedin'] && $this->_tpl_vars['currencies']): ?>
		<form method="post" action="domainchecker.php" class="form-horizontal">
			<?php echo $this->_tpl_vars['LANG']['choosecurrency']; ?>
: <select class="input-sm" name="currency" onchange="submit()" style="width:76px;">
			<?php $_from = $this->_tpl_vars['currencies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['curr']):
?>
				<option value="<?php echo $this->_tpl_vars['curr']['id']; ?>
"<?php if ($this->_tpl_vars['curr']['id'] == $this->_tpl_vars['currency']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['curr']['code']; ?>
</option>
			<?php endforeach; endif; unset($_from); ?>
			</select>
		</form>
	<?php endif; ?>


<?php endif; ?>

<div class="modal fade in" id="modalpleasewait">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header text-center">
                 <h4><i class="fa fa-spinner fa-spin"></i> <?php echo $this->_tpl_vars['LANG']['pleasewait']; ?>
</h4>
            </div>
        </div>
    </div>
</div>