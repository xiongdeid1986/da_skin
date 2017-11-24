<?php /* Smarty version 2.6.28, created on 2017-03-10 20:40:54
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/clientareadomains.tpl */ ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/pageheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['LANG']['clientareanavdomains'],'desc' => $this->_tpl_vars['LANG']['clientareadomainsintro'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="row clearfix">
	<div class="col-lg-3 col-md-4 col-sm-4 pull-right">
		<form method="post" action="clientarea.php?action=domains">
			<div class="input-group">
				<input type="text" placeholder="<?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
" name="q" value="<?php if ($this->_tpl_vars['q']): ?><?php echo $this->_tpl_vars['q']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
<?php endif; ?>" class="form-control search-query" onfocus="if(this.value=='<?php echo $this->_tpl_vars['LANG']['searchenterdomain']; ?>
')this.value=''" /><span class="input-group-btn"><button type="submit" class="btn btn-primary"><i class="fa fa-search icon-only"></i></button></span>
			</div>
		</form>
	</div>
</div>

<div class="space-12"></div>

<?php echo '
<script>
$(document).ready(function() {
	$(".setbulkaction").click(function(event) {
	  event.preventDefault();
	  $("#bulkaction").val($(this).attr(\'id\'));
	  $("#bulkactionform").submit();
	});
});
</script>
'; ?>



<p><span class="badge badge-primary"><?php echo $this->_tpl_vars['numitems']; ?>
</span> <?php echo $this->_tpl_vars['LANG']['recordsfound']; ?>
, <?php echo $this->_tpl_vars['LANG']['page']; ?>
 <?php echo $this->_tpl_vars['pagenumber']; ?>
 <?php echo $this->_tpl_vars['LANG']['pageof']; ?>
 <?php echo $this->_tpl_vars['totalpages']; ?>
</p>
<form method="post" id="bulkactionform" action="clientarea.php?action=bulkdomain">
<input id="bulkaction" name="update" type="hidden" />
<table class="table table-bordered table-hover dataTable tc-table">
	<thead>
		<tr>
			<th class="col-small center">
				<input type="Checkbox" class="tc" />
				<span class="labels"></span>
			</th>
			<th<?php if ($this->_tpl_vars['orderby'] == 'domain'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
"<?php endif; ?> class="sorting"><a href="clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=domain"><?php echo $this->_tpl_vars['LANG']['clientareahostingdomain']; ?>
</a></th>
			<th<?php if ($this->_tpl_vars['orderby'] == 'regdate'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=regdate"><?php echo $this->_tpl_vars['LANG']['clientareahostingregdate']; ?>
</a></th>
			<th<?php if ($this->_tpl_vars['orderby'] == 'nextduedate'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=nextduedate"><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
</a></th>
			<th<?php if ($this->_tpl_vars['orderby'] == 'status'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=status"><?php echo $this->_tpl_vars['LANG']['clientareastatus']; ?>
</a></th>
			<th<?php if ($this->_tpl_vars['orderby'] == 'autorenew'): ?> class="sorting_<?php echo $this->_tpl_vars['sort']; ?>
 hidden-sm hidden-xs visible-lg visible-md"<?php endif; ?> class="sorting hidden-sm hidden-xs visible-lg visible-md"><a href="clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&orderby=autorenew"><?php echo $this->_tpl_vars['LANG']['domainsautorenew']; ?>
</a></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<?php $_from = $this->_tpl_vars['domains']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['domain']):
?>
		<tr>
			<td class="col-small center">
				<input type="Checkbox" class="tc" name="domids[]" class="domids" value="<?php echo $this->_tpl_vars['domain']['id']; ?>
" />
				<span class="labels"></span>
			</td>
			<td><a href="clientarea.php?action=domaindetails&id=<?php echo $this->_tpl_vars['domain']['id']; ?>
"><?php echo $this->_tpl_vars['domain']['domain']; ?>
</a>						
				<ul class="list-unstyled visible-sm visible-xs hidden-lg hidden-md">
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small><?php echo $this->_tpl_vars['LANG']['clientareahostingregdate']; ?>
: <?php echo $this->_tpl_vars['domain']['registrationdate']; ?>
</i></small></li>
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small><?php echo $this->_tpl_vars['LANG']['clientareahostingnextduedate']; ?>
: <?php echo $this->_tpl_vars['domain']['nextduedate']; ?>
</i></small></li>
					<li><span class="label label-<?php echo $this->_tpl_vars['domain']['rawstatus']; ?>
 arrowed-in-right arrowed-in"><?php echo $this->_tpl_vars['domain']['statustext']; ?>
</span></li>
					<li><i class="fa fa-angle-right bigger-110"></i> <i><small><?php echo $this->_tpl_vars['LANG']['domainsautorenew']; ?>
: <?php if ($this->_tpl_vars['domain']['autorenew']): ?><?php echo $this->_tpl_vars['LANG']['domainsautorenewenabled']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['domainsautorenewdisabled']; ?>
<?php endif; ?></i></small></li>
				</ul>					
			</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['domain']['registrationdate']; ?>
</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md"><?php echo $this->_tpl_vars['domain']['nextduedate']; ?>
</td>
			<td class="hidden-sm hidden-xs visible-lg visible-md"><span class="label label-<?php echo $this->_tpl_vars['domain']['rawstatus']; ?>
 arrowed-in-right arrowed-in"><?php echo $this->_tpl_vars['domain']['statustext']; ?>
</span></td>
			<td class="hidden-sm hidden-xs visible-lg visible-md"><?php if ($this->_tpl_vars['domain']['autorenew']): ?><?php echo $this->_tpl_vars['LANG']['domainsautorenewenabled']; ?>
<?php else: ?><?php echo $this->_tpl_vars['LANG']['domainsautorenewdisabled']; ?>
<?php endif; ?></td>
			<td class="col-small center">
				<div class="action-buttons">
					<a href="clientarea.php?action=domaindetails&id=<?php echo $this->_tpl_vars['domain']['id']; ?>
" class="tooltip-primary" data-rel="tooltip" data-placement="left" title="<?php echo $this->_tpl_vars['LANG']['managedomain']; ?>
"><i class="fa fa-edit bigger-130"></i></a>
				</div>
			</td>
		</tr>
	<?php endforeach; else: ?>
		<tr>
			<td colspan="7" class="text-center"><?php echo $this->_tpl_vars['LANG']['norecordsfound']; ?>
</td>
		</tr>
	<?php endif; unset($_from); ?>
	<tfoot>
		<tr>
			<td class="col-small center"></td>
			<td colspan="6">
				<div class="btn-group dropup">
					<a class="btn btn-default btn-sm" href="#" data-toggle="dropdown"><?php echo $this->_tpl_vars['LANG']['withselected']; ?>
<i class="fa fa-angle-down icon-on-right"></i></a>
						<ul class="dropdown-menu  dropdown-caret dropdown-menu-right">
							<li><a href="#" id="nameservers" class="setbulkaction"><?php echo $this->_tpl_vars['LANG']['domainmanagens']; ?>
</a></li>
							<li><a href="#" id="autorenew" class="setbulkaction"><?php echo $this->_tpl_vars['LANG']['domainautorenewstatus']; ?>
</a></li>
							<li><a href="#" id="reglock" class="setbulkaction"><?php echo $this->_tpl_vars['LANG']['domainreglockstatus']; ?>
</a></li>
							<li><a href="#" id="contactinfo" class="setbulkaction"><?php echo $this->_tpl_vars['LANG']['domaincontactinfo']; ?>
</a></li>
							<li class="divider"></li>
							<?php if ($this->_tpl_vars['allowrenew']): ?><li><a href="#" id="renew" class="setbulkaction"><?php echo $this->_tpl_vars['LANG']['domainmassrenew']; ?>
</a></li><?php endif; ?>
						</ul>
				</div>
			</td>
		</tr>
	</tfoot>
</table>

</form>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['template'])."/clientarearecordslimit.tpl", 'smarty_include_vars' => array('clientareaaction' => $this->_tpl_vars['clientareaaction'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<ul class="pagination no-margin">
	<li class="prev<?php if (! $this->_tpl_vars['prevpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['prevpage']): ?>clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['prevpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>">&larr; <?php echo $this->_tpl_vars['LANG']['previouspage']; ?>
</a></li>
	<li class="next<?php if (! $this->_tpl_vars['nextpage']): ?> disabled<?php endif; ?>"><a href="<?php if ($this->_tpl_vars['nextpage']): ?>clientarea.php?action=domains<?php if ($this->_tpl_vars['q']): ?>&q=<?php echo $this->_tpl_vars['q']; ?>
<?php endif; ?>&amp;page=<?php echo $this->_tpl_vars['nextpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>"><?php echo $this->_tpl_vars['LANG']['nextpage']; ?>
 &rarr;</a></li>
</ul>
