<?php /* Smarty version 2.6.28, created on 2016-12-15 17:34:52
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/knowledgebase.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/knowledgebase.tpl', 47, false),)), $this); ?>


<div class="padding-25">
    <div class="row">
		<div class="col-md-7 center-block">
        <form method="post" action="knowledgebase.php?action=search">
            <div class="input-group">
                <input class="form-control input-lg" name="search" type="text" value="<?php echo $this->_tpl_vars['LANG']['kbquestionsearchere']; ?>
" onfocus="this.value=(this.value=='<?php echo $this->_tpl_vars['LANG']['kbquestionsearchere']; ?>
') ? '' : this.value;" onblur="this.value=(this.value=='') ? '<?php echo $this->_tpl_vars['LANG']['kbquestionsearchere']; ?>
' : this.value;"/>
                <span class="input-group-btn">
					<button type="submit" class="btn btn-lg btn-inverse" value="" /><i class="fa fa-search icon-only"></i></button>
				</span>
            </div>
        </form>
		</div>
    </div>
</div>

<div class="block-s3">
	<h3><?php echo $this->_tpl_vars['LANG']['knowledgebasecategories']; ?>
</h3>
	<div class="row">
		<?php $_from = $this->_tpl_vars['kbcats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['kbasecats'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['kbasecats']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['kbcat']):
        $this->_foreach['kbasecats']['iteration']++;
?>
			<div class="col-sm-4">
				<div class="well white">
				<h4><a href="<?php if ($this->_tpl_vars['seofriendlyurls']): ?>knowledgebase/<?php echo $this->_tpl_vars['kbcat']['id']; ?>
/<?php echo $this->_tpl_vars['kbcat']['urlfriendlyname']; ?>
<?php else: ?>knowledgebase.php?action=displaycat&amp;catid=<?php echo $this->_tpl_vars['kbcat']['id']; ?>
<?php endif; ?>"><?php echo $this->_tpl_vars['kbcat']['name']; ?>
</a> <small>(<?php echo $this->_tpl_vars['kbcat']['numarticles']; ?>
)</small></h4>
				<?php echo $this->_tpl_vars['kbcat']['description']; ?>

				</div>
			</div>
		<?php endforeach; endif; unset($_from); ?>
	</div>
</div>

<div class="block-s2">

	<div class="padding-all">
		<h3><?php echo $this->_tpl_vars['LANG']['knowledgebasepopular']; ?>
</h3>
		<?php $_from = $this->_tpl_vars['kbmostviews']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['kbarticle']):
?>
			<p>
				<a href="<?php if ($this->_tpl_vars['seofriendlyurls']): ?>knowledgebase/<?php echo $this->_tpl_vars['kbarticle']['id']; ?>
/<?php echo $this->_tpl_vars['kbarticle']['urlfriendlytitle']; ?>
.html<?php else: ?>knowledgebase.php?action=displayarticle&amp;id=<?php echo $this->_tpl_vars['kbarticle']['id']; ?>
<?php endif; ?>"><?php echo $this->_tpl_vars['kbarticle']['title']; ?>
</a><br />
				<?php echo ((is_array($_tmp=$this->_tpl_vars['kbarticle']['article'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 100, "...") : smarty_modifier_truncate($_tmp, 100, "...")); ?>

			</p>
		<?php endforeach; endif; unset($_from); ?>
	</div>
</div>