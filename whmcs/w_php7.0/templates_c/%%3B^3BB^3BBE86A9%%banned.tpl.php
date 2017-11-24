<?php /* Smarty version 2.6.28, created on 2016-12-19 18:27:49
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/banned.tpl */ ?>

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="page-header">
			<h1><?php echo $this->_tpl_vars['LANG']['accessdenied']; ?>
</h1>
		</div>
		<div class="alert alert-danger">
			<h4><?php echo $this->_tpl_vars['LANG']['bannedyourip']; ?>
 <?php echo $this->_tpl_vars['ip']; ?>
 <?php echo $this->_tpl_vars['LANG']['bannedhasbeenbanned']; ?>
</h4>
			<ul>
				<li><?php echo $this->_tpl_vars['LANG']['bannedbanreason']; ?>
: <?php echo $this->_tpl_vars['reason']; ?>
</li>
				<li><?php echo $this->_tpl_vars['LANG']['bannedbanexpires']; ?>
: <?php echo $this->_tpl_vars['expires']; ?>
</li>
			</ul>
		</div>
	</div>
</div>