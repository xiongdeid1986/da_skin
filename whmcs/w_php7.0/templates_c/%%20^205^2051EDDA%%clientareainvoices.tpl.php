<?php /* Smarty version 2.6.28, created on 2016-12-17 18:02:04
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareainvoices.tpl */ ?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['invoices'],'desc' => $this->_tpl_vars['LANG']['invoicesintro'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<div class="row">
	<div class="col-xs-12 col-sm-12">
		<p class="pull-left"><span class="badge badge-primary"><?php echo $this->_tpl_vars['numitems']; ?>
</span> <?php echo $this->_tpl_vars['LANG']['recordsfound']; ?>
, <?php echo $this->_tpl_vars['LANG']['page']; ?>
 <?php echo $this->_tpl_vars['pagenumber']; ?>
 <?php echo $this->_tpl_vars['LANG']['pageof']; ?>
 <?php echo $this->_tpl_vars['totalpages']; ?>
</p>
		<p class="pull-right"><?php echo $this->_tpl_vars['LANG']['invoicesoutstandingbalance']; ?>
: <span class="label label-lg arrowed-right label-<?php if ($this->_tpl_vars['nobalance']): ?>success<?php else: ?>danger<?php endif; ?>"><?php echo $this->_tpl_vars['totalbalance']; ?>
</span><?php if ($this->_tpl_vars['masspay']): ?>&nbsp; <a href="clientarea.php?action=masspay&all=true" class="btn btn-success"><i class="fa fa-check-circle-o"></i> <?php echo $this->_tpl_vars['LANG']['masspayall']; ?>
</a><?php endif; ?></p>
	</div>
</div>

<p><?php echo $this->_tpl_vars['LANG']['invoicescredit']; ?>
 <?php echo $this->_tpl_vars['LANG']['invoicesbalance']; ?>
:<span class="label label-inverse label-xlg arrowed-in-right arrowed-in"> <?php echo $this->_tpl_vars['clientsstats']['creditbalance']; ?>
</span></p>

<form method="post" action="clientarea.php?action=masspay">
	<table class="table table-bordered table-striped table-hover dataTable tc-table">
		<thead>
			<tr>
				<?php if ($this->_tpl_vars['masspay']): ?>
				<th class="col-small center">
					<input type="checkbox" class="tc"/>
					<span class="labels"></span>
				</th><?php endif; ?>
				<th<?php if ($this->_tpl_vars['orderby'] == 'id'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
"<?php endif; ?> class="sorting"><a href="clientarea.php?action=invoices&orderby=id"><?php echo $this->_tpl_vars['LANG']['invoicestitle']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'date'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=date"><?php echo $this->_tpl_vars['LANG']['invoicesdatecreated']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'duedate'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=duedate"><?php echo $this->_tpl_vars['LANG']['invoicesdatedue']; ?>
</a></th>
				<th<?php if ($this->_tpl_vars['orderby'] == 'status'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=status"><?php echo $this->_tpl_vars['LANG']['invoicesstatus']; ?>
</a></th>						
				<th<?php if ($this->_tpl_vars['orderby'] == 'total'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=invoices&orderby=total"><?php echo $this->_tpl_vars['LANG']['invoicestotal']; ?>
</a></th>
				<th class="col-small center">&nbsp;</th>
			</tr>
		</thead>				
		<?php $_from = $this->_tpl_vars['invoices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['invoice']):
?>
			<tr>
				<?php if ($this->_tpl_vars['masspay']): ?>
				<td class="col-small center">
					<input type="checkbox" class="invoiceids tc" name="invoiceids[]" value="<?php echo $this->_tpl_vars['invoice']['id']; ?>
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
						<li><i class="fa fa-angle-right bigger-110"></i> <?php echo $this->_tpl_vars['LANG']['invoicesdatecreated']; ?>
: <?php echo $this->_tpl_vars['invoice']['datecreated']; ?>
</li>
						<li><i class="fa fa-angle-right bigger-110"></i> <?php echo $this->_tpl_vars['LANG']['invoicesdatedue']; ?>
: <?php echo $this->_tpl_vars['invoice']['datedue']; ?>
</li>
						<li><i class="fa fa-angle-right bigger-110"></i> <?php echo $this->_tpl_vars['LANG']['invoicestotal']; ?>
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
				<td colspan="<?php if ($this->_tpl_vars['masspay']): ?>7<?php else: ?>6<?php endif; ?>" class="text-center"><?php echo $this->_tpl_vars['LANG']['norecordsfound']; ?>
</td>
			</tr>
		<?php endif; unset($_from); ?>
				
        <?php if ($this->_tpl_vars['masspay']): ?>
        <tfoot>
            <tr>
                <td class="col-small center"></td>
                <td colspan="5"><input type="submit" name="masspayselected" value="<?php echo $this->_tpl_vars['LANG']['masspayselected']; ?>
" class="btn btn-sm btn-inverse">&nbsp;&nbsp;<a href="clientarea.php?action=masspay&amp;all=true" class="btn btn-sm btn-success"><i class="fa fa-check-circle-o"></i> <?php echo $this->_tpl_vars['LANG']['masspayall']; ?>
</a>
                <td class="hidden-sm hidden-xs visible-lg visible-md"></td>
            </tr>
         </tfoot><?php endif; ?>
	</table>			
</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/clientarearecordslimit.tpl", 'smarty_include_vars' => array('clientareaaction' => $this->_tpl_vars['clientareaaction'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<ul class="pagination">
	<li class="prev<?php if (! $this->_tpl_vars['prevpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['prevpage']): ?>clientarea.php?action=invoices<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['prevpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>">&larr; <?php echo $this->_tpl_vars['LANG']['previouspage']; ?>
</a></li>
	<li class="next<?php if (! $this->_tpl_vars['nextpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['nextpage']): ?>clientarea.php?action=invoices<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['nextpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>"><?php echo $this->_tpl_vars['LANG']['nextpage']; ?>
 &rarr;</a></li>
</ul>