<?php /* Smarty version 2.6.28, created on 2016-12-15 18:28:32
         compiled from /home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/announcements.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/announcements.tpl', 13, false),array('modifier', 'strip_tags', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/announcements.tpl', 14, false),array('modifier', 'truncate', '/home/ddwebcom/domains/cloud.ddweb.com.cn/public_html/templates/webhoster/announcements.tpl', 14, false),)), $this); ?>

<?php $_from = $this->_tpl_vars['announcements']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['num'] => $this->_tpl_vars['announcement']):
?>
<div class="block-s3 no-padding-top">
<h3><a href="<?php if ($this->_tpl_vars['seofriendlyurls']): ?>announcements/<?php echo $this->_tpl_vars['announcement']['id']; ?>
/<?php echo $this->_tpl_vars['announcement']['urlfriendlytitle']; ?>
.html<?php else: ?><?php echo $_SERVER['PHP_SELF']; ?>
?id=<?php echo $this->_tpl_vars['announcement']['id']; ?>
<?php endif; ?>" class=""><?php echo $this->_tpl_vars['announcement']['title']; ?>
</a></h3>
<p><i class="fa fa-calendar text-success"></i> &nbsp;<?php echo ((is_array($_tmp=$this->_tpl_vars['announcement']['timestamp'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%A, %B %e, %Y") : smarty_modifier_date_format($_tmp, "%A, %B %e, %Y")); ?>
</p>
<p><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['announcement']['text'])) ? $this->_run_mod_handler('strip_tags', true, $_tmp) : smarty_modifier_strip_tags($_tmp)))) ? $this->_run_mod_handler('truncate', true, $_tmp, 400, "...") : smarty_modifier_truncate($_tmp, 400, "...")); ?>
</p>
<?php if (strlen ( $this->_tpl_vars['announcement']['text'] ) > 300): ?><p><div class="action-buttons"><a href="<?php if ($this->_tpl_vars['seofriendlyurls']): ?>announcements/<?php echo $this->_tpl_vars['announcement']['id']; ?>
/<?php echo $this->_tpl_vars['announcement']['urlfriendlytitle']; ?>
.html<?php else: ?><?php echo $_SERVER['PHP_SELF']; ?>
?id=<?php echo $this->_tpl_vars['announcement']['id']; ?>
<?php endif; ?>" ><?php echo $this->_tpl_vars['LANG']['more']; ?>
 <i class="fa fa-angle-double-right"></i></a></div></p><?php endif; ?>
</div>

<div class="hr hr-6 dotted hr-double"></div>

<?php if ($this->_tpl_vars['facebookrecommend']): ?>
<?php echo '
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>
'; ?>

<div class="fb-like" data-href="<?php echo $this->_tpl_vars['systemurl']; ?>
<?php if ($this->_tpl_vars['seofriendlyurls']): ?>announcements/<?php echo $this->_tpl_vars['announcement']['id']; ?>
/<?php echo $this->_tpl_vars['announcement']['urlfriendlytitle']; ?>
.html<?php else: ?>announcements.php?id=<?php echo $this->_tpl_vars['announcement']['id']; ?>
<?php endif; ?>" data-send="true" data-width="450" data-show-faces="true" data-action="recommend"></div>
<?php endif; ?>
<br /><br />
<?php endforeach; else: ?>
<p align="center"><strong><?php echo $this->_tpl_vars['LANG']['announcementsnone']; ?>
</strong></p>
<?php endif; unset($_from); ?>

<br />
<div class="action-buttons pull-right"><a href="announcementsrss.php"><i class="fa fa-rss text-orange"></i> <?php echo $this->_tpl_vars['LANG']['announcementsrss']; ?>
</a></div>
<div class="clearfix"></div>

   <ul class="pagination">
      <li<?php if (! $this->_tpl_vars['prevpage']): ?> class="disabled"<?php endif; ?>>
			<a href="<?php if ($this->_tpl_vars['prevpage']): ?><?php echo $_SERVER['PHP_SELF']; ?>
?page=<?php echo $this->_tpl_vars['prevpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>" title="<?php echo $this->_tpl_vars['LANG']['previouspage']; ?>
">&larr; <?php echo $this->_tpl_vars['LANG']['previouspage']; ?>
</a>
      </li>
      <li<?php if (! $this->_tpl_vars['nextpage']): ?> class="disabled"<?php endif; ?>>
			<a href="<?php if ($this->_tpl_vars['nextpage']): ?><?php echo $_SERVER['PHP_SELF']; ?>
?page=<?php echo $this->_tpl_vars['nextpage']; ?>
<?php else: ?>javascript:return false;<?php endif; ?>" title="<?php echo $this->_tpl_vars['LANG']['nextpage']; ?>
"><?php echo $this->_tpl_vars['LANG']['nextpage']; ?>
 &rarr;</a>
      </li>
   </ul>

<br />