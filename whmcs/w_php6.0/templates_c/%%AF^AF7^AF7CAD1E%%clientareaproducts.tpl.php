<?php /* Smarty version 2.6.28, created on 2016-12-13 23:53:07
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareaproducts.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['clientareaproducts'],'desc' => $this->_tpl_vars['LANG']['clientareaproductsintro'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="row clearfix">
	<div class="col-lg-3 col-md-4 col-sm-4 pull-right">
		<form method="post" action="clientarea.php?action=products">
			<div class="input-group">
				<input type="text" name="q" placeholder="<?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
" value="<?php if ($this->_tpl_vars['q']): ?><?php echo $this->_tpl_vars['q']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
<?php endif; ?>" class="form-control search-query" onfocus="if(this.value=='<?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
')this.value=''" /><span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search icon-only"></i></button></span>
			</div>
		</form>
	</div>
</div>

<div class="space-12"></div>


	<p><span class="badge badge-primary"><?php echo $this->_tpl_vars['numitems']; ?>
</span> <?php echo $this->_tpl_vars['LANG']['recordsfound']; ?>
, <?php echo $this->_tpl_vars['LANG']['page']; ?>
 <?php echo $this->_tpl_vars['pagenumber']; ?>
 <?php echo $this->_tpl_vars['LANG']['pageof']; ?>
 <?php echo $this->_tpl_vars['totalpages']; ?>
</p>

	<table class="table table-bordered table-hover dataTable tc-table">
		<thead>
			<tr>
				<th<?php if ($this->_tpl_vars['orderby'] == 'product'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
"<?php endif; ?> class="sorting"><a href="clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=product"><?php echo $this->_tpl_vars['LANG']['orderproduct']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'price'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=price"><?php echo $this->_tpl_vars['LANG']['orderprice']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'billingcycle'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=billingcycle"><?php echo $this->_tpl_vars['LANG']['orderbillingcycle']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'nextduedate'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=nextduedate"><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'status'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
"<?php endif; ?> class="sorting"><a href="clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=status"><?php echo $this->_tpl_vars['LANG']['clientareastatus']; ?>
</a></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<?php $_from = $this->_tpl_vars['services']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['service']):
?>
			<tr>
				<td><a href="clientarea.php?action=productdetails&id=<?php echo $this->_tpl_vars['service']['id']; ?>
"><?php echo $this->_tpl_vars['service']['group']; ?>
 - <?php echo $this->_tpl_vars['service']['product']; ?>
</a><?php if ($this->_tpl_vars['service']['domain']): ?><br /><i><small><?php echo $this->_tpl_vars['service']['domain']; ?>
</i></small><?php endif; ?>
					<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small><?php echo $this->_tpl_vars['LANG']['orderprice']; ?>
: <?php echo $this->_tpl_vars['service']['amount']; ?>
</i></small></li>
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small><?php echo $this->_tpl_vars['LANG']['orderbillingcycle']; ?>
: <?php echo $this->_tpl_vars['service']['billingcycle']; ?>
</i></small></li>
						<li><i class="fa fa-angle-right bigger-110 text-green"></i> <i><small><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
: <?php echo $this->_tpl_vars['service']['nextduedate']; ?>
</i></small></li>
					</ul>					
				</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['service']['amount']; ?>
</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['service']['billingcycle']; ?>
</td>
				<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['service']['nextduedate']; ?>
</td>
				<td><span class="label label-<?php echo $this->_tpl_vars['service']['rawstatus']; ?>
 arrowed-in-right arrowed-in"><?php echo $this->_tpl_vars['service']['statustext']; ?>
</span></td>
				<td class="col-small center">					
					<div class="action-buttons">
						<a href="clientarea.php?action=productdetails&id=<?php echo $this->_tpl_vars['service']['id']; ?>
" class="tooltip-primary" data-rel="tooltip" data-placement="left" title="<?php echo $this->_tpl_vars['LANG']['clientareaviewdetails']; ?>
"><i class="fa fa-search-plus bigger-130"></i></a>	
					</div>
				</td>								
			</tr>
			<?php endforeach; else: ?>
		<tr>
			<td colspan="6" class="text-center"><?php echo $this->_tpl_vars['LANG']['norecordsfound']; ?>
</td>
		</tr>
	<?php endif; unset($_from); ?>
	</table>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/clientarearecordslimit.tpl", 'smarty_include_vars' => array('clientareaaction' => $this->_tpl_vars['clientareaaction'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<ul class="pagination no-margin">
		<li class="prev<?php if (! $this->_tpl_vars['prevpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['prevpage']): ?>clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['prevpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>">&larr; <?php echo $this->_tpl_vars['LANG']['previouspage']; ?>
</a></li>
		<li class="next<?php if (! $this->_tpl_vars['nextpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['nextpage']): ?>clientarea.php?action=products<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['nextpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>"><?php echo $this->_tpl_vars['LANG']['nextpage']; ?>
 &rarr;</a></li>
	</ul>
