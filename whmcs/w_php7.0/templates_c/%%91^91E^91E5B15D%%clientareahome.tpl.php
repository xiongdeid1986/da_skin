<?php /* Smarty version 2.6.28, created on 2016-12-13 23:52:56
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareahome.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'sprintf2', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareahome.tpl', 22, false),array('modifier', 'strip_tags', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareahome.tpl', 117, false),array('modifier', 'truncate', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareahome.tpl', 117, false),)), $this); ?>

<div class="page-header no-margin-top">
	<h3><?php echo $this->_tpl_vars['pagetitle']; ?>
 <small><span class="toggle" data-toggle="dash-intro"><i class="fa fa-question-circle"></i></span></small></h3>
</div>


<div class="note hide" id="dash-intro">
	<?php echo $this->_tpl_vars['LANG']['clientareaheader']; ?>

</div>

<?php if ($this->_tpl_vars['ccexpiringsoon']): ?>
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p><strong><?php echo $this->_tpl_vars['LANG']['ccexpiringsoon']; ?>
:</strong></p><p><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['ccexpiringsoondesc'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, '</p><p><a href="clientarea.php?action=creditcard" class="btn btn-mini">', '</a>') : smarty_modifier_sprintf2($_tmp, '</p><p><a href="clientarea.php?action=creditcard" class="btn btn-mini">', '</a>')); ?>
</p>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['clientsstats']['incredit']): ?>
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['availcreditbaldesc'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['clientsstats']['creditbalance']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['clientsstats']['creditbalance'])); ?>
</p>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['clientsstats']['numoverdueinvoices'] > 0): ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
		<p><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['youhaveoverdueinvoices'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, $this->_tpl_vars['clientsstats']['numoverdueinvoices']) : smarty_modifier_sprintf2($_tmp, $this->_tpl_vars['clientsstats']['numoverdueinvoices'])); ?>
:</strong> <?php echo ((is_array($_tmp=$this->_tpl_vars['LANG']['overdueinvoicesdesc'])) ? $this->_run_mod_handler('sprintf2', true, $_tmp, ' <a href="clientarea.php?action=masspay&all=true">', '</a>') : smarty_modifier_sprintf2($_tmp, ' <a href="clientarea.php?action=masspay&all=true">', '</a>')); ?>
</p>
	</div>
<?php endif; ?>

<div class="row">
	<div class="<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans'] || $this->_tpl_vars['condlinks']['domainown']): ?>col-lg-3 col-sm-6<?php else: ?>col-lg-4<?php endif; ?>">
		<a class="tile-button btn btn-white" href="<?php if ($this->_tpl_vars['clientsstats']['productsnumtotal'] > 0): ?>clientarea.php?action=products<?php else: ?>cart.php<?php endif; ?>">
			<div class="tile-content-wrapper">
				<i class="fa fa-cogs text-primary"></i>				
				<div class="tile-content text-primary">
					<?php echo $this->_tpl_vars['clientsstats']['productsnumactive']; ?>
<span>(<?php echo $this->_tpl_vars['clientsstats']['productsnumtotal']; ?>
)</span>
				</div>
				<small><?php echo $this->_tpl_vars['LANG']['clientareanavservices']; ?>
</small>				
			</div>
		</a>
	</div>
	<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans'] || $this->_tpl_vars['condlinks']['domainown']): ?>		
	<div class="col-lg-3 col-sm-6">
		<a class="tile-button btn btn-white" href="<?php if ($this->_tpl_vars['clientsstats']['numdomains'] > 0): ?>clientarea.php?action=domains<?php else: ?>domainchecker.php<?php endif; ?>">
			<div class="tile-content-wrapper">
				<i class="fa fa-globe"></i>	
				<div class="tile-content">
					<?php echo $this->_tpl_vars['clientsstats']['numactivedomains']; ?>
<span>(<?php echo $this->_tpl_vars['clientsstats']['numdomains']; ?>
)</span>
				</div>
				<small><?php echo $this->_tpl_vars['LANG']['clientareanavdomains']; ?>
</small>
			</div>
		</a>
	</div>
	<?php endif; ?>
	  
	<div class="<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans'] || $this->_tpl_vars['condlinks']['domainown']): ?>col-lg-3 col-sm-6<?php else: ?>col-lg-4<?php endif; ?>">
		<a class="tile-button btn btn-white" href="<?php if ($this->_tpl_vars['clientsstats']['numtickets'] > 0): ?>supporttickets.php<?php else: ?>submitticket.php<?php endif; ?>">
			<div class="tile-content-wrapper">
				<i class="fa fa-comments text-success"></i>
				<div class="tile-content text-success">
					<?php echo $this->_tpl_vars['clientsstats']['numtickets']; ?>

				</div>
				<small><?php echo $this->_tpl_vars['LANG']['navtickets']; ?>
</small>
			</div>
		</a>
	</div>
		  
	<?php if ($this->_tpl_vars['condlinks']['affiliates']): ?>
	<div class="<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans'] || $this->_tpl_vars['condlinks']['domainown']): ?>col-lg-3 col-sm-6<?php else: ?>col-lg-4<?php endif; ?>">
		<a class="tile-button btn btn-white" href="affiliates.php">
			<div class="tile-content-wrapper">
				<i class="fa fa-users text-warning"></i>
				<div class="tile-content text-warning">
					<?php echo $this->_tpl_vars['clientsstats']['numaffiliatesignups']; ?>

				</div>
				<small><?php echo $this->_tpl_vars['LANG']['affiliatestitle']; ?>
</small>
			</div>
		</a>
	</div>
	<?php else: ?>
	<div class="<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans'] || $this->_tpl_vars['condlinks']['domainown']): ?>col-lg-3 col-sm-6<?php else: ?>col-lg-4<?php endif; ?>">
		<a class="tile-button btn btn-white" href="clientarea.php?action=invoices">
			<div class="tile-content-wrapper">
				<i class="fa fa-warning text-danger"></i>
				<div class="tile-content text-danger">
					<?php echo $this->_tpl_vars['clientsstats']['numdueinvoices']; ?>

				</div>
				<small><?php echo $this->_tpl_vars['LANG']['invoicesdue']; ?>
</small>
			</div>
		</a>
	</div>
	<?php endif; ?>		  
</div><!-- /.row -->

<?php if ($this->_tpl_vars['announcements']): ?>
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-list"></i> <?php echo $this->_tpl_vars['LANG']['ourlatestnews']; ?>
</h4>
		</div>
		<div class="portlet-widgets">
			<a class="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
		<span class="divider"></span>
	</div>
	<div class="portlet-body">
		<div id="owl-example" class="owl-carousel">
			<div><i class="fa fa-clock-o"></i> <a href="announcements.php?id=<?php echo $this->_tpl_vars['announcements']['0']['id']; ?>
"><?php echo $this->_tpl_vars['announcements']['0']['date']; ?>
</a> <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['announcements']['0']['text'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 500, '...') : smarty_modifier_truncate($_tmp, 500, '...')); ?>
</div>
			<?php if ($this->_tpl_vars['announcements']['1']['text']): ?><div><i class="fa fa-clock-o"></i> <a href="announcements.php?id=<?php echo $this->_tpl_vars['announcements']['1']['id']; ?>
"><?php echo $this->_tpl_vars['announcements']['1']['date']; ?>
</a> <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['announcements']['1']['text'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 500, '...') : smarty_modifier_truncate($_tmp, 500, '...')); ?>
</div><?php endif; ?>
			<?php if ($this->_tpl_vars['announcements']['2']['text']): ?><div><i class="fa fa-clock-o"></i> <a href="announcements.php?id=<?php echo $this->_tpl_vars['announcements']['2']['id']; ?>
"><?php echo $this->_tpl_vars['announcements']['2']['date']; ?>
</a> <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['announcements']['2']['text'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 500, '...') : smarty_modifier_truncate($_tmp, 500, '...')); ?>
</div><?php endif; ?>
		</div>
	</div>
</div>

<?php echo '<script>$(document).ready(function() {
  var owl = $("#owl-example");owl.owlCarousel({autoHeight : true, singleItem:true, pagination: false, transitionStyle: "fade" });
  $(".next").click(function(){owl.trigger(\'owl.next\');})
  $(".prev").click(function(){owl.trigger(\'owl.prev\');})
});</script>'; ?>

<?php endif; ?>

<?php $_from = $this->_tpl_vars['addons_html']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['addon_html']):
?>
<div style="margin:15px 0 15px 0;"><?php echo $this->_tpl_vars['addon_html']; ?>
</div>
<?php endforeach; endif; unset($_from); ?>
<?php if (in_array ( 'tickets' , $this->_tpl_vars['contactpermissions'] )): ?>
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-comments"></i> <?php echo $this->_tpl_vars['LANG']['supportticketsopentickets']; ?>
</h4>
		</div>
		<div class="portlet-widgets">
			<a href="submitticket.php" class="tooltip-primary" data-placement="top" data-rel="tooltip" title="" data-original-title="<?php echo $this->_tpl_vars['LANG']['opennewticket']; ?>
"><i class="fa fa-plus"></i></a>
			<span class="divider"></span>
			<a data-toggle="collapse" data-parent="#accordion" href="#ticket-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="ticket-box" class="panel-collapse collapse in">
		<div class="portlet-body no-padding">
			<table class="table table-bordered table-hover tc-table">
				<thead>
					<tr>
						<th><?php echo $this->_tpl_vars['LANG']['supportticketssubject']; ?>
</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['supportticketsstatus']; ?>
</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['supportticketsdepartment']; ?>
</th>
						<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['supportticketsticketlastupdated']; ?>
</th>
						<th class="col-small center">&nbsp;</th>
					</tr>
				</thead>
			<?php $_from = $this->_tpl_vars['tickets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['ticket']):
?>
				<tr>
					<td><a href="viewticket.php?tid=<?php echo $this->_tpl_vars['ticket']['tid']; ?>
&amp;c=<?php echo $this->_tpl_vars['ticket']['c']; ?>
"><?php if ($this->_tpl_vars['ticket']['unread']): ?><strong><?php endif; ?>#<?php echo $this->_tpl_vars['ticket']['tid']; ?>
 - <?php echo $this->_tpl_vars['ticket']['subject']; ?>
<?php if ($this->_tpl_vars['ticket']['unread']): ?></strong><?php endif; ?></a>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['supportticketsticketlastupdated']; ?>
: <?php echo $this->_tpl_vars['ticket']['lastreply']; ?>
</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['supportticketsdepartment']; ?>
: <?php echo $this->_tpl_vars['ticket']['department']; ?>
</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['supportticketsstatus']; ?>
: <?php echo $this->_tpl_vars['ticket']['status']; ?>
</li>
						</ul>																							
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['ticket']['status']; ?>
</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['ticket']['department']; ?>
</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['ticket']['lastreply']; ?>
</td>
					<td class="col-small center"><div class="action-buttons"><a href="viewticket.php?tid=<?php echo $this->_tpl_vars['ticket']['tid']; ?>
&c=<?php echo $this->_tpl_vars['ticket']['c']; ?>
"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
				</tr>
			<?php endforeach; else: ?>
				<tr>
					<td colspan="6" class="text-center"><?php echo $this->_tpl_vars['LANG']['supportticketsnoneopen']; ?>
</td>
				</tr>
			<?php endif; unset($_from); ?>
			</table>
		</div>
	</div>
</div>
<div style="margin-top:15px;"></div>
<?php endif; ?>




<?php if (in_array ( 'invoices' , $this->_tpl_vars['contactpermissions'] )): ?>
<form method="post" action="clientarea.php?action=masspay">
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-<?php if ($this->_tpl_vars['clientsstats']['numdueinvoices'] > 0): ?>warning<?php else: ?>check<?php endif; ?>"></i> <?php echo $this->_tpl_vars['LANG']['invoicesdue']; ?>
</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#invoice-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="invoice-box" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-bordered table-hover tc-table">
			<thead>
				<tr>
					<?php if ($this->_tpl_vars['masspay']): ?>
					<th class="col-small center">
						<input type="checkbox" class="tc" />
						<span class="labels"></span>
					</th><?php endif; ?>
					<th><?php echo $this->_tpl_vars['LANG']['invoicenumber']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['invoicesdatecreated']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['invoicesdatedue']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['invoicesstatus']; ?>
</th>
					<th class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['LANG']['invoicestotal']; ?>
</th>
					<th class="col-small center">&nbsp;</th>
				</tr>
			</thead>
		<?php $_from = $this->_tpl_vars['invoices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['invoice']):
?>			
				<tr>
					<?php if ($this->_tpl_vars['masspay']): ?>
					<td class="col-small center">
						<input type="checkbox" class="tc" name="invoiceids[]" value="<?php echo $this->_tpl_vars['invoice']['id']; ?>
" <?php if ($this->_tpl_vars['invoice']['rawstatus'] != 'unpaid'): ?> disabled<?php endif; ?> />
						<span class="labels"></span>
					</td><?php endif; ?>
					<td><a href="viewinvoice.php?id=<?php echo $this->_tpl_vars['invoice']['id']; ?>
" target="_blank"><?php echo $this->_tpl_vars['invoice']['invoicenum']; ?>
</a>
						<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
							<li><span class="label label-<?php echo $this->_tpl_vars['invoice']['rawstatus']; ?>
 arrowed-in-right arrowed-in"><?php echo $this->_tpl_vars['invoice']['statustext']; ?>
</span></li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['invoicesdatecreated']; ?>
: <?php echo $this->_tpl_vars['invoice']['datecreated']; ?>
</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['invoicesdatedue']; ?>
: <?php echo $this->_tpl_vars['invoice']['datedue']; ?>
</li>
							<li><i class="fa fa-angle-right bigger-110 text-green"></i><?php echo $this->_tpl_vars['LANG']['invoicestotal']; ?>
: <?php echo $this->_tpl_vars['invoice']['total']; ?>
</li>
						</ul>
					</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['invoice']['datecreated']; ?>
</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['invoice']['datedue']; ?>
</td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><span class="label label-<?php echo $this->_tpl_vars['invoice']['rawstatus']; ?>
 arrowed-in-right arrowed-in"><?php echo $this->_tpl_vars['invoice']['statustext']; ?>
</span></td>
					<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['invoice']['total']; ?>
</td>
					<td class="col-small center"><div class="action-buttons"><a href="viewinvoice.php?id=<?php echo $this->_tpl_vars['invoice']['id']; ?>
" target="_blank"><i class="fa fa-search-plus bigger-130"></i></a></div></td>
				</tr>
		<?php endforeach; else: ?>
				<tr>
					<td class="text-center" colspan="<?php if ($this->_tpl_vars['masspay']): ?>8<?php else: ?>7<?php endif; ?>"><?php echo $this->_tpl_vars['LANG']['invoicesnoneunpaid']; ?>
</td>
				</tr>
		<?php endif; unset($_from); ?>			
		<?php if ($this->_tpl_vars['clientsstats']['numoverdueinvoices'] > 0): ?>
		<?php if ($this->_tpl_vars['masspay']): ?>
			<tfoot>
				<tr>
					<td class="col-small center"></td>
					<td colspan="5"><input type="submit" name="masspayselected" value="<?php echo $this->_tpl_vars['LANG']['masspayselected']; ?>
" class="btn btn-inverse">&nbsp;&nbsp;<a href="clientarea.php?action=masspay&amp;all=true" class="btn btn-success"><i class="fa fa-check-circle-o"></i> <?php echo $this->_tpl_vars['LANG']['masspayall']; ?>
</a>
					<td class="hidden-sm hidden-xs visible-lg visible-md"></td>
				</tr>
			</tfoot>
		<?php endif; ?>
		<?php else: ?>
			<?php endif; ?>
		</table>
	</div>
	</div>
</div>
</form>
<?php endif; ?>

<?php if ($this->_tpl_vars['files']): ?>
<div class="portlet">
	<div class="portlet-heading dark">
		<div class="portlet-title">
			<h4><i class="fa fa-paperclip"></i> <?php echo $this->_tpl_vars['LANG']['clientareafiles']; ?>
</h4>
		</div>
		<div class="portlet-widgets">
			<a data-toggle="collapse" data-parent="#accordion" href="#file-box"><i class="fa fa-chevron-down"></i></a>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="file-box" class="panel-collapse collapse in">
	<div class="portlet-body no-padding">
		<table class="table table-striped table-bordered table-hover tc-table">
			<thead>
				<tr>
					<th class="col-medium"><?php echo $this->_tpl_vars['LANG']['clientareafilesdate']; ?>
</th>
					<th><?php echo $this->_tpl_vars['LANG']['clientareafilesfilename']; ?>
</th>
					</tr>
			</thead>
			<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['file']):
?>
				<tr>
					<td class="col-medium" data-title="<?php echo $this->_tpl_vars['LANG']['clientareafilesdate']; ?>
"><?php echo $this->_tpl_vars['file']['date']; ?>
</td>
					<td data-title="<?php echo $this->_tpl_vars['LANG']['clientareafilesfilename']; ?>
"><div class="action-buttons"><a href="dl.php?type=f&id=<?php echo $this->_tpl_vars['file']['id']; ?>
"><i class="fa fa-download"></i> <?php echo $this->_tpl_vars['file']['title']; ?>
</a></div></td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>
	</div>
	</div>
</div>
<?php endif; ?>