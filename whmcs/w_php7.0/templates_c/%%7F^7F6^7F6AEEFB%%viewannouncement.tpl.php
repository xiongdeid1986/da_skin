<?php /* Smarty version 2.6.28, created on 2016-12-14 02:02:10
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/viewannouncement.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/viewannouncement.tpl', 12, false),)), $this); ?>

<div class="block-s2 no-padding-top">
	<h2><?php echo $this->_tpl_vars['title']; ?>
</h2>
		<p><i class="fa fa-calendar text-success"></i>  &nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['timestamp'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%A, %B %e, %Y") : smarty_modifier_date_format($_tmp, "%A, %B %e, %Y")); ?>
</p>
	<hr />
	<?php echo $this->_tpl_vars['text']; ?>

<br /><br />
</div>

<div class="well">
	<div class="row">
<?php if ($this->_tpl_vars['twittertweet']): ?>
		<div class="col-sm-2 text-left">
			<div class="tweetbutton" style="display:inline-block;"><a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo $this->_tpl_vars['twitterusername']; ?>
">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script></div>
		</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['facebookrecommend']): ?>
		<div class="col-sm-8 text-center">
			<?php echo '<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>'; ?>

			<div class="fb-like" data-href="<?php echo $this->_tpl_vars['systemurl']; ?>
<?php if ($this->_tpl_vars['seofriendlyurls']): ?>announcements/<?php echo $this->_tpl_vars['id']; ?>
/<?php echo $this->_tpl_vars['urlfriendlytitle']; ?>
.html<?php else: ?>announcements.php?id=<?php echo $this->_tpl_vars['id']; ?>
<?php endif; ?>" data-send="true" data-width="450" data-show-faces="true" data-action="recommend"></div>
		</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['googleplus1']): ?>
		<div class="col-sm-2 text-right">
			<g:plusone data-size="small"></g:plusone>
			<?php echo '<script type="text/javascript">(function() { var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true; po.src = \'https://apis.google.com/js/plusone.js\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s); })();</script>'; ?>

		</div>
<?php endif; ?>
	</div>
</div>


<?php if ($this->_tpl_vars['facebookcomments']): ?>
<div class="block-s3 no-padding-bottom hidden-xs">
	<div id="fb-root"></div>
	<?php echo '<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) {return;} js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>'; ?>

	<fb:comments href="<?php echo $this->_tpl_vars['systemurl']; ?>
<?php if ($this->_tpl_vars['seofriendlyurls']): ?>announcements/<?php echo $this->_tpl_vars['id']; ?>
/<?php echo $this->_tpl_vars['urlfriendlytitle']; ?>
.html<?php else: ?>announcements.php?id=<?php echo $this->_tpl_vars['id']; ?>
<?php endif; ?>" num_posts="5" width="500"></fb:comments>
</div>
<?php endif; ?>

<p class="text-center"><a href="announcements.php" class="btn btn-inverse"><?php echo $this->_tpl_vars['LANG']['clientareabacklink']; ?>
</a></p>