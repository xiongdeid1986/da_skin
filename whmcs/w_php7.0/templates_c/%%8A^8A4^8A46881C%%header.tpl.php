<?php /* Smarty version 2.6.28, created on 2016-12-13 17:28:33
         compiled from webhoster/header.tpl */ ?>
<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->_tpl_vars['charset']; ?>
" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ($this->_tpl_vars['filename'] == index && ( $_GET['search'] == "" ) && ( $_GET['action'] == "" )): ?>
		
		<title><?php if ($this->_tpl_vars['kbarticle']['title']): ?><?php echo $this->_tpl_vars['kbarticle']['title']; ?>
 - <?php endif; ?><?php echo $this->_tpl_vars['pagetitle']; ?>
 - <?php echo $this->_tpl_vars['companyname']; ?>
</title>
		<meta name="keywords" content="<?php echo $this->_tpl_vars['LANG']['meta_keywords_homepage']; ?>
" />
		<meta name="description" content="<?php echo $this->_tpl_vars['LANG']['meta_description_homepage']; ?>
" />
		
		<?php else: ?>
		
		<title><?php if ($this->_tpl_vars['kbarticle']['title']): ?><?php echo $this->_tpl_vars['kbarticle']['title']; ?>
 - <?php endif; ?><?php echo $this->_tpl_vars['pagetitle']; ?>
 - <?php echo $this->_tpl_vars['companyname']; ?>
</title>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['systemurl']): ?><base href="<?php echo $this->_tpl_vars['systemurl']; ?>
" /><?php endif; ?>
	
		<!-- basic styles -->
		<link href="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		
		<link rel="stylesheet" href="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/plugins/owl-carousel/owl.carousel.css">
		
		
		<link id="whstyle" rel="stylesheet" href="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style.css">
		<link rel="stylesheet" href="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/whmcs.css" />
		
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/jquery.min.js"></script>
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/bootstrap.min.js"></script>
		
		<?php if ($this->_tpl_vars['livehelpjs']): ?><?php echo $this->_tpl_vars['livehelpjs']; ?>
<?php endif; ?>
		
		<!--[if lt IE 9]>
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/html5shiv.js"></script>
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/respond.min.js"></script>
		<![endif]-->
		
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/whmcs.js"></script>
		<link rel="shortcut icon" href="templates/<?php echo $this->_tpl_vars['template']; ?>
/favicon.ico">

		<?php echo $this->_tpl_vars['headoutput']; ?>

	</head>
	
	<body class="webhoster">
	<?php echo $this->_tpl_vars['headeroutput']; ?>
		
    <nav class="navbar top-navbar dark-menu navbar-fixed-top" role="navigation">
	
		<div class="pre-header">
			<div class="container">
				<div class="row">
					<!-- BEGIN TOP BAR LEFT PART -->
					<div class="col-xs-5">
						<ul class="list-unstyled list-inline hidden-xs hidden-sm">


						
							<!--- Chnage your Phone number and Email here Here --->
							<li><i class="fa fa-phone"></i><span>400-100-5392</span></li>
							<li><i class="fa fa-envelope-o"></i><span>service@ddweb.com.cn</span></li>
							
						</ul>
						<ul class="list-unstyled list-inline visible-xs visible-sm">
						
							<!--- Chnage your Phone number and Email here Here --->
							<li>
								<span class="tooltip-primary" data-placement="right" data-rel="tooltip" title="400-100-5392">
									<i class="fa fa-phone"></i>
								</span>
							</li>
							<li>
								<span class="tooltip-primary" data-placement="right" data-rel="tooltip" title="service@ddweb.com.cn">
									<i class="fa fa-envelope-o"></i>	
								</span>
							</li>
						</ul>
					</div>
					<!-- END TOP BAR LEFT PART -->
					
					
					
					
					<!-- BEGIN TOP BAR MENU -->
					<div class="col-xs-7 additional-nav">
						<?php foreach ($_SESSION['cart']['products'] as $prodkey => $prodval){if ($prodval['noconfig'] != '1'){if ($prodval['qty'] > 1){$cartcount = $cartcount + $prodval['qty'];}else{$cartcount = $cartcount + count($prodkey);}}}foreach ($_SESSION['cart']['domains'] as $domkey => $domval){if (array_key_exists('dnsmanagement', $domval)){$cartcount = $cartcount + count($domkey);}}foreach ($_SESSION['cart']['addons'] as $addkey => $addval){$cartcount = $cartcount + count($addkey);}foreach ($_SESSION['cart']['renewals'] as $addkey => $addval){$cartcount = $cartcount + count($addkey);}if ($cartcount){$this->_tpl_vars['cartcount'] = $cartcount;} ?>
						<ul class="list-unstyled list-inline pull-right">
							
							<?php if ($this->_tpl_vars['cartcount'] > 0): ?>
							<li class="dropdown">
								<a href="cart.php?a=view">
									<i class="fa fa-shopping-cart"></i> <span class="badge up badge-success"><?php echo $this->_tpl_vars['cartcount']; ?>
</span></a>
								</a>
							</li>
							<?php endif; ?>
							<?php if ($this->_tpl_vars['clientsstats']['numactivetickets'] > 0): ?>
							<li class="dropdown">
								<a href="support-tickets">
									<i class="fa fa-comments-o"></i> <span class="badge up badge-primary"><?php echo $this->_tpl_vars['clientsstats']['numoverdueinvoices']; ?>
</span>
								</a>
							</li>
							<?php endif; ?>
						
							<?php if ($this->_tpl_vars['clientsstats']['numoverdueinvoices'] > 0): ?>
							<li class="dropdown">
								<a href="clientarea.php?action=invoices">
									<i class="fa fa-warning"></i> <span class="badge up badge-danger"><?php echo $this->_tpl_vars['clientsstats']['numoverdueinvoices']; ?>
</span>
								</a>
							</li>
							<?php endif; ?>
							
							<?php if ($this->_tpl_vars['livehelp']): ?>
							<li><a href="http://www.ddweb.com.cn/" id="Menu-Live_Chat" class="LiveHelpButton"><i class="fa fa-comments text-warning"></i> <span class="hidden-xs">Live Chat</span></a></li>
							<?php else: ?>
							<li><a href="http://www.ddweb.com.cn/"><i class="fa fa-comments text-warning"></i> <span class="hidden-xs">Live Chat</span></a></li>
							<?php endif; ?>
						</ul>
					</div>
					<!-- END TOP BAR MENU -->
				</div>
			</div>        
		</div>

        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="index.php">
                    <img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/images/logo.png" alt="<?php echo $this->_tpl_vars['companyname']; ?>
" class="img-responsive" />
                </a>
				
				 <!-- Top Menu Right-->
				<ul class="nav navbar-right">
				
					<!--Search Box-->
					<li>
						<div class="nav-search">
							<form method="post" class="" action="knowledgebase.php?action=search">
								<div class="form-group">
									<input type="text" placeholder="<?php echo $this->_tpl_vars['LANG']['knowledgebasesearch']; ?>
 ..." name="search" class="form-control" autocomplete="off" />
									<span class="glyphicon glyphicon-search text-primary"></span>
								</div>							
							</form>
						</div>
					</li>
					<!--Search Box-->
					
					<li class="dropdown user-box-no-images">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="fa fa-user"></i> <span class="user-info hidden-xs">My <?php echo $this->_tpl_vars['LANG']['account']; ?>
</span> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu dropdown-user">
							<?php if ($this->_tpl_vars['loggedin']): ?>
							<li><a href="clientarea.php"><?php echo $this->_tpl_vars['LANG']['clientareatitle']; ?>
</a></li>
							<li><a href="clientarea.php?action=details"><?php echo $this->_tpl_vars['LANG']['editaccountdetails']; ?>
</a></li>
							<li><a href="clientarea.php?action=contacts"><?php echo $this->_tpl_vars['LANG']['clientareanavcontacts']; ?>
</a></li>
							<li><a href="clientarea.php?action=emails"><?php echo $this->_tpl_vars['LANG']['navemailssent']; ?>
</a></li>
							<li><a href="clientarea.php?action=changepw"><?php echo $this->_tpl_vars['LANG']['clientareanavchangepw']; ?>
</a></li>
							<li><a href="logout.php"><?php echo $this->_tpl_vars['LANG']['logouttitle']; ?>
</a></li>
							<?php else: ?>
							<li><a href="clientarea.php"><?php echo $this->_tpl_vars['LANG']['login']; ?>
</a></li>
							<li><a href="register.php"><?php echo $this->_tpl_vars['LANG']['register']; ?>
</a></li>
							<li><a href="pwreset.php"><?php echo $this->_tpl_vars['LANG']['forgotpw']; ?>
</a></li>
							<?php endif; ?>
						</ul>
					</li>

				</ul>
				
            </div>		
            <!-- End Top Menu Right-->
			<div class="nav-top">
			
				<!-- Top Menu Left-->
				<div class="top-menu collapse navbar-collapse  navbar-main-collapse">
					<ul class="nav navbar-nav navbar-left">

						<li class="dropdown"><a href="index.php">首页</a></li>
						<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->_tpl_vars['LANG']['navservices']; ?>
 <b class="caret"></b> </a>
						  <ul class="dropdown-menu">
						    <?php if ($this->_tpl_vars['loggedin']): ?>
						    <li><a id="Menu-Services-My_Services" href="clientarea.php?action=products"><?php echo $this->_tpl_vars['LANG']['clientareanavservices']; ?>
</a></li>
						      <li><a id="Menu-Services-Order_New_Services" href="cart.php"><?php echo $this->_tpl_vars['LANG']['navservicesorder']; ?>
</a></li>
						      <li><a id="Menu-Services-View_Available_Addons" href="cart.php?gid=addons"><?php echo $this->_tpl_vars['LANG']['clientareaviewaddons']; ?>
</a></li>												
							  <?php endif; ?>		
					        </ul>
					  </li>
				  
					  			<?php if ($this->_tpl_vars['condlinks']['domainreg'] || $this->_tpl_vars['condlinks']['domaintrans']): ?>
						<li class="dropdown"><a id="Menu-Domains" class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $this->_tpl_vars['LANG']['navdomains']; ?>
&nbsp;<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php if ($this->_tpl_vars['loggedin']): ?>
								<li><a id="Menu-Domains-My_Domains" href="clientarea.php?action=domains"><?php echo $this->_tpl_vars['LANG']['clientareanavdomains']; ?>
</a></li>
								<li><a id="Menu-Domains-Renew_Domains" href="cart.php?gid=renewals"><?php echo $this->_tpl_vars['LANG']['navrenewdomains']; ?>
</a></li>
								<?php endif; ?>
								<?php if ($this->_tpl_vars['condlinks']['domainreg']): ?><li><a id="Menu-Domains-Register_a_New_Domain" href="cart.php?a=add&domain=register"><?php echo $this->_tpl_vars['LANG']['navregisterdomain']; ?>
</a></li><?php endif; ?>
								<?php if ($this->_tpl_vars['condlinks']['domaintrans']): ?><li><a id="Menu-Domains-Transfer_Domains_to_Us" href="cart.php?a=add&domain=transfer"><?php echo $this->_tpl_vars['LANG']['navtransferdomain']; ?>
</a></li><?php endif; ?>
								<li><a id="Menu-Domains-Whois_Lookup" href="domainchecker.php"><?php echo $this->_tpl_vars['LANG']['navwhoislookup']; ?>
</a></li>
							</ul>
						</li>		
						<?php endif; ?>
	  
					  
						<?php if ($this->_tpl_vars['loggedin']): ?>
						<li class="dropdown">
							<a id="Menu-Billing" class="dropdown-toggle" data-toggle="dropdown" href="#">
								<?php echo $this->_tpl_vars['LANG']['navbilling']; ?>
&nbsp;<b class="caret"></b>							</a>
							<ul class="dropdown-menu">
								<li><a id="Menu-Billing-My_Invoices" href="clientarea.php?action=invoices"><?php echo $this->_tpl_vars['LANG']['invoices']; ?>
</a></li>
								<li><a id="Menu-Billing-My_Quotes" href="clientarea.php?action=quotes"><?php echo $this->_tpl_vars['LANG']['quotestitle']; ?>
</a></li>
								<?php if ($this->_tpl_vars['condlinks']['addfunds']): ?><li><a id="Menu-Billing-Add_Funds" href="clientarea.php?action=addfunds"><?php echo $this->_tpl_vars['LANG']['addfunds']; ?>
</a></li><?php endif; ?>
								<?php if ($this->_tpl_vars['condlinks']['masspay']): ?><li><a id="Menu-Billing-Mass_Payment" href="clientarea.php?action=masspay&all=true"><?php echo $this->_tpl_vars['LANG']['masspaytitle']; ?>
</a></li><?php endif; ?>
								<?php if ($this->_tpl_vars['condlinks']['updatecc']): ?><li><a id="Menu-Billing-Manage_Credit_Card" href="clientarea.php?action=creditcard"><?php echo $this->_tpl_vars['LANG']['navmanagecc']; ?>
</a></li><?php endif; ?>
								<?php if ($this->_tpl_vars['condlinks']['affiliates']): ?><li><a href="affiliates.php"><?php echo $this->_tpl_vars['LANG']['affiliatestitle']; ?>
</a></li><?php endif; ?>
							</ul>
						</li>							
						<?php endif; ?>
								
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php echo $this->_tpl_vars['LANG']['navsupport']; ?>
 <b class="caret"></b>							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a id="Menu-Support-Tickets" href="supporttickets.php"><?php echo $this->_tpl_vars['LANG']['navtickets']; ?>
</a></li>
								<li><a id="Menu-Support-Knowledgebase" href="knowledgebase.php"><?php echo $this->_tpl_vars['LANG']['knowledgebasetitle']; ?>
</a></li>
								<li><a id="Menu-Support-Network_Status" href="serverstatus.php"><?php echo $this->_tpl_vars['LANG']['networkstatustitle']; ?>
</a></li>
								<li><a id="Menu-Annoucements" href="announcements.php"><?php echo $this->_tpl_vars['LANG']['announcementstitle']; ?>
</a></li>
								<li><a id="Menu-Support-Downloads" href="downloads.php"><?php echo $this->_tpl_vars['LANG']['downloadstitle']; ?>
</a></li>
								<!--<?php if ($this->_tpl_vars['loggedin']): ?><li><a id="Menu-Contact_Us" href="submitticket.php?step=2&deptid=1"><?php echo $this->_tpl_vars['LANG']['contactus']; ?>
</a></li><?php endif; ?>-->
							</ul>								
						</li>

						<!--About us Page links-->
						<li><a href="announcements.php?id=1">Tos条款</a></li>
						<li><a href="submitticket.php?step=2&deptid=1">联系我们</a></li>
						
						<!--<?php if ($this->_tpl_vars['loggedin']): ?><?php else: ?><li><a id="Menu-Contact_Us" href="contact.php"><?php echo $this->_tpl_vars['LANG']['contactus']; ?>
</a></li><?php endif; ?>-->
					</ul>
			  </div>
				<!-- Top Menu Left-->
				
			</div>
            <!-- /.Top Menu -->
		</div>
        <!-- /.container -->
    </nav>


	<div class="page-container"><!-- /page container -->
	
	<!-- /#layout-button -->	
		<div class="qs-layout-menu front">
			<div class="btn btn-gray qs-setting-btn" id="qs-setting-btn">
				<i class="fa fa-cog bigger-150 icon-only"></i>
			</div>
			<div class="qs-setting-box" id="qs-setting-box">
				<div style="margin-bottom:10px;"><?php if ($this->_tpl_vars['langchange']): ?><?php echo $this->_tpl_vars['setlanguage']; ?>
<?php endif; ?></div>
				<span class="bigger-120">Color Options</span>										
				<div class="hr hr-dotted hr-8"></div>										
				<ul>									
					<li><button class="btn" style="background-color:#3498db;" onClick="swapStyle('templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style.css')"></button></li>
					<li><button class="btn" style="background-color:#86618f;" onClick="swapStyle('templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style-1.css')"></button></li> 
					<li><button class="btn" style="background-color:#ba5d32;" onClick="swapStyle('templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style-2.css')"></button></li>
					<li><button class="btn" style="background-color:#488075;" onClick="swapStyle('templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style-3.css')"></button></li>
					<li><button class="btn" style="background-color:#4e72c2;" onClick="swapStyle('templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/css/themes/style-4.css')"></button></li>
				</ul>											
			</div>
		</div>
	<!-- /#layout-button -->
		
		<?php if ($this->_tpl_vars['filename'] == index && ( $_GET['search'] == "" ) && ( $_GET['action'] == "" ) || $this->_tpl_vars['filename'] == 'web_hosting' || $this->_tpl_vars['filename'] == 'web_hosting_windows' || $this->_tpl_vars['filename'] == 'reseller_hosting' || $this->_tpl_vars['filename'] == 'alpha_reseller' || $this->_tpl_vars['filename'] == 'master_reseller' || $this->_tpl_vars['filename'] == 'ssl_certificates' || $this->_tpl_vars['filename'] == 'vps_hosting' || $this->_tpl_vars['filename'] == 'dedicated_servers'): ?><?php else: ?>
		<div class="mass-head hero-1">
			<div class="container">
				<div class="hero-inner text-center">
					<h1>
						<?php if ($this->_tpl_vars['pagetitle'] == $this->_tpl_vars['LANG']['supportticketspagetitle'] || $this->_tpl_vars['pagetitle'] == $this->_tpl_vars['LANG']['supportticketsviewticket'] || $this->_tpl_vars['pagetitle'] == $this->_tpl_vars['LANG']['supportticketssubmitticket']): ?>
							<i class="fa fa-ticket"></i>
						<?php elseif ($this->_tpl_vars['pagetitle'] == $this->_tpl_vars['LANG']['knowledgebasetitle']): ?>
							<i class="fa fa-question-circle"></i>
						<?php elseif ($this->_tpl_vars['pagetitle'] == $this->_tpl_vars['LANG']['announcementstitle']): ?>
							<i class="fa fa-bullhorn"></i>
						<?php endif; ?>
						<?php echo $this->_tpl_vars['pagetitle']; ?>

					</h1>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['filename'] == index && ( $_GET['search'] == "" ) && ( $_GET['action'] == "" ) || $this->_tpl_vars['filename'] == 'web_hosting' || $this->_tpl_vars['filename'] == 'web_hosting_windows' || $this->_tpl_vars['filename'] == 'reseller_hosting' || $this->_tpl_vars['filename'] == 'alpha_reseller' || $this->_tpl_vars['filename'] == 'master_reseller' || $this->_tpl_vars['filename'] == 'ssl_certificates' || $this->_tpl_vars['filename'] == 'vps_hosting' || $this->_tpl_vars['filename'] == 'dedicated_servers'): ?><?php else: ?>
		<div class="container">
			<div class="block-s3">
			
		<?php endif; ?>