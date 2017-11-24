<?php /* Smarty version 2.6.28, created on 2016-12-15 17:31:38
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/downloads.tpl */ ?>

<p><?php echo $this->_tpl_vars['LANG']['downloadsintrotext']; ?>
</p>

<div class="padding-25">
    <div class="row">
		<div class="col-md-7 center-block">
			<form method="post" action="downloads.php?action=search">
				<div class="input-group">
					<input type="text" name="search" value="<?php echo $this->_tpl_vars['LANG']['downloadssearch']; ?>
" class="form-control input-lg" onfocus="if(this.value=='<?php echo $this->_tpl_vars['LANG']['downloadssearch']; ?>
')this.value=''" />
					<span class="input-group-btn">
						<button type="submit" class="btn btn-lg btn-inverse"><i class="fa fa-search icon-only"></i></button>
					</span>
					</div>
			</form>
		</div>
	</div>
</div>

<div class="block-s3">
	<h3><?php echo $this->_tpl_vars['LANG']['downloadscategories']; ?>
</h4>
	<div class="row">
	<?php $_from = $this->_tpl_vars['dlcats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dlcat']):
?>
		<div class="col-sm-4">
			<div class="well white">
				<h4><a href="<?php if ($this->_tpl_vars['seofriendlyurls']): ?>downloads/<?php echo $this->_tpl_vars['dlcat']['id']; ?>
/<?php echo $this->_tpl_vars['dlcat']['urlfriendlyname']; ?>
<?php else: ?>downloads.php?action=displaycat&amp;catid=<?php echo $this->_tpl_vars['dlcat']['id']; ?>
<?php endif; ?>"><?php echo $this->_tpl_vars['dlcat']['name']; ?>
</a> (<?php echo $this->_tpl_vars['dlcat']['numarticles']; ?>
)</h4>
					<?php echo $this->_tpl_vars['dlcat']['description']; ?>

			</div>
		</div>
	<?php endforeach; endif; unset($_from); ?>
	</div>
</div>

<div class="block-s2">
`	<h3><?php echo $this->_tpl_vars['LANG']['downloadspopular']; ?>
</h3>
	<?php $_from = $this->_tpl_vars['mostdownloads']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['download']):
?>
		<h5><?php echo $this->_tpl_vars['download']['type']; ?>
 <a href="<?php echo $this->_tpl_vars['download']['link']; ?>
"><?php echo $this->_tpl_vars['download']['title']; ?>
<?php if ($this->_tpl_vars['download']['clientsonly']): ?> <img src="images/padlock.gif" alt="<?php echo $this->_tpl_vars['LANG']['loginrequired']; ?>
" /><?php endif; ?></a></h5>
		<div><?php echo $this->_tpl_vars['download']['description']; ?>
</div>
		<small><?php echo $this->_tpl_vars['LANG']['downloadsfilesize']; ?>
: <?php echo $this->_tpl_vars['download']['filesize']; ?>
</small>
	<?php endforeach; endif; unset($_from); ?>
</div>