<?php
/* Smarty version 3.1.29, created on 2017-11-28 01:21:23
  from "/home/cloud.ddweb.com.cn/public_html/templates/NeWorld/NeWorld/NeWorld-header.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a1cba13287414_70553404',
  'file_dependency' => 
  array (
    'f9ee0a691a49965e24acf0ef9e049a6e25f5e62a' => 
    array (
      0 => '/home/cloud.ddweb.com.cn/public_html/templates/NeWorld/NeWorld/NeWorld-header.tpl',
      1 => 1511832028,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a1cba13287414_70553404 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/home/cloud.ddweb.com.cn/public_html/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
?>
<header>
	<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?><div class="wave-wrap"><?php }?>
	    <div class="container">
	    	<nav class="navbar navbar-inverse nav-home">
	            <!-- Brand and toggle get grouped for better mobile display -->
	
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse" aria-expanded="false">
					<label class="ac-gn-menuicon-label">
						<span class="ac-gn-menuicon-bread ac-gn-menuicon-bread-top">
							<span class="ac-gn-menuicon-bread-crust ac-gn-menuicon-bread-crust-top"></span>
						</span>
						<span class="ac-gn-menuicon-bread ac-gn-menuicon-bread-bottom">
							<span class="ac-gn-menuicon-bread-crust ac-gn-menuicon-bread-crust-bottom"></span>
						</span>
					</label>
				</button>
				
	            <div class="navbar-header">
	                <a class="navbar-brand" href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
</a>
	            </div><!-- Collect the nav links, forms, and other content for toggling -->
	
	            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse">
	                <ul class="nav navbar-nav navbar-right">
	                    <li <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'features') {?> class="active"<?php }?>>
	                    	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
features.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['features'];?>
</a>
	                    </li>
	                    <li <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'pricing') {?> class="active"<?php }?>>
	                    	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
pricing.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['pricing'];?>
</a>
	                    </li>
	                    <li class="dropdown<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'vps') {?> active<?php }?>">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['hosting'];?>
 <span class="caret"></span></a>
	
	                        <ul class="dropdown-menu">
	                            <li><a href="/shadowsocks/"><i class="fa fa-paper-plane"></i> Shadowsocks</a></li>
	
	                            <li><a href="/sharehosting/"><i class="fa fa-group"></i> Shared Hosting</a></li>
	
	                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
vps.php"><i class="fa fa-cloud"></i> VPS Hosting</a></li>
	
	                            <li><a href="/dedicated/"><i class="fa fa-server"></i> Dedicated Hosting</a></li>
	                        </ul>
	                    </li>
	                    <li <?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'contact') {?> class="active"<?php }?>>
	                    	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
contact.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['homecontact'];?>
</a>
	                    </li>
					<?php if ($_smarty_tpl->tpl_vars['loggedin']->value) {?>
	                    <li>
	                        <a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_smarty_tpl->tpl_vars['clientsdetails']->value['lastname'];
echo $_smarty_tpl->tpl_vars['clientsdetails']->value['firstname'];?>
 <span class="caret"></span></a>
	
	                        <ul class="dropdown-menu">
		                        <li>
		                        	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php"><i class="md md-home"></i> <?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientareatitle'];?>
</a>
		                        </li>
		                        <li>
		                        	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php?action=details"><i class="md md-face-unlock"></i> <?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientareanavdetails'];?>
</a>
		                        </li>
				                <li>
				                	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php?action=contacts"><i class="md md-account-box"></i> <?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientareanavcontacts'];?>
</a>
				                </li>
				                <li>
				                	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php?action=changepw"><i class="md md-settings"></i> <?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientareanavchangepw'];?>
</a>
				                </li>
				                <?php if ($_smarty_tpl->tpl_vars['condlinks']->value['updatecc']) {?>
				                <li>
				                	<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php?action=creditcard"><i class="md md-credit-card"></i><?php echo $_smarty_tpl->tpl_vars['LANG']->value['navmanagecc'];?>
</a>
				                </li>
				                <?php }?>
								<?php if ($_smarty_tpl->tpl_vars['condlinks']->value['addfunds']) {?>
								<li>
									<a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
clientarea.php?action=addfunds"><i class="md md-account-balance-wallet"></i> <?php echo $_smarty_tpl->tpl_vars['LANG']->value['addfunds'];?>
</a>
								</li>
								<?php }?>
								<li><a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
logout.php"><i class="md md-settings-power"></i><?php echo $_smarty_tpl->tpl_vars['LANG']->value['logouttitle'];?>
</a></li>
	                        </ul>
	                    </li>
						<li><a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
cart.php?a=view" class="btn btn-border"><i class="fa fa-shopping-cart"></i><span id="cartItemCount" class="badge badge-danger"><?php echo $_smarty_tpl->tpl_vars['cartitemcount']->value;?>
</span></a></li>
					<?php } else { ?>
						<li><a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
login.php"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientlogin'];?>
</a></li>
						<li class="hidden-sm"><a href="<?php echo $_smarty_tpl->tpl_vars['systemurl']->value;?>
register.php" class="btn btn-border"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['clientregistertitle'];?>
</a></li>
					<?php }?>
	                </ul>
	            </div><!-- /.navbar-collapse -->
	    	</nav>
	<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?>
	    	<div class="home-slider space3x">
		    	<div class="col-sm-6 col-md-5">
		    		<h2 class="wow fadeInDown">See Our Simple Pricing,<br/>No Bandwidth Overages!</h2>
		    		<p class="wow fadeInDown">Form early to enterprise, we've got you covered Starts with 14 days free. Annual payment earns you two months free!</p>
		    		<a href="#" class="btn btn-success"><?php echo $_smarty_tpl->tpl_vars['LANG']->value['getstarted'];?>
</a>
		    	</div>
	    	</div>
	<?php }?>
	    </div><!-- /.container -->
	<?php if ($_smarty_tpl->tpl_vars['templatefile']->value == 'homepage') {?></div><?php }?>
</header>

<?php if ($_smarty_tpl->tpl_vars['announcements']->value) {?>
<section class="announcements">
	<div class="container">
		<h2><?php echo $_smarty_tpl->tpl_vars['LANG']->value['homeannouncements'];?>
</h2>
		<ul>
	    <?php
$_from = $_smarty_tpl->tpl_vars['announcements']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_announcement_0_saved_item = isset($_smarty_tpl->tpl_vars['announcement']) ? $_smarty_tpl->tpl_vars['announcement'] : false;
$_smarty_tpl->tpl_vars['announcement'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['announcement']->index=-1;
$_smarty_tpl->tpl_vars['announcement']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['announcement']->value) {
$_smarty_tpl->tpl_vars['announcement']->_loop = true;
$_smarty_tpl->tpl_vars['announcement']->index++;
$__foreach_announcement_0_saved_local_item = $_smarty_tpl->tpl_vars['announcement'];
?>
	        <?php if ($_smarty_tpl->tpl_vars['announcement']->index < 3) {?>
            <li>
            	[<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['announcement']->value['rawDate'],"m-d");?>
] <a href="<?php if ($_smarty_tpl->tpl_vars['seofriendlyurls']->value) {?>announcements/<?php echo $_smarty_tpl->tpl_vars['announcement']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['announcement']->value['urlfriendlytitle'];?>
.html<?php } else { ?>announcements.php?id=<?php echo $_smarty_tpl->tpl_vars['announcement']->value['id'];
}?>"><?php echo $_smarty_tpl->tpl_vars['announcement']->value['title'];?>
</a>
            </li>
	        <?php }?>
	    <?php
$_smarty_tpl->tpl_vars['announcement'] = $__foreach_announcement_0_saved_local_item;
}
if ($__foreach_announcement_0_saved_item) {
$_smarty_tpl->tpl_vars['announcement'] = $__foreach_announcement_0_saved_item;
}
?>
		</ul>
	</div>
</section>
<?php }
}
}
