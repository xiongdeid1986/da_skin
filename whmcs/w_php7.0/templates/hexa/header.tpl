<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9"> <![endif]-->
<head>
  <meta charset="UTF-8">
  <title>{$companyname} - {$pagetitle}{if $kbarticle.title} - {$kbarticle.title}{/if}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {if $systemurl}<base href="{$systemurl}" />
  {/if}
  <link rel='stylesheet' id='bootstrap-css'  href='templates/{$template}/css/bootstrap.min.css' type='text/css' media='all' />
  <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="templates/{$template}/css/simple-line-icons.css">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="templates/{$template}/css/owl.carousel.css">
  <link rel='stylesheet' href="templates/{$template}/css/animate.min.css" />
  <link rel='stylesheet' id='hexa-css'  href='templates/{$template}/css/whmcs.css' type='text/css' media='all' />
  <script src="templates/{$template}/js/jquery.js"></script>
  {$headoutput}
</head>
<body>
  {if $formaction == 'dologin.php' || $filename == 'logout' || $filename == 'pwreset' || $filename == 'contact'}
  <div class="hexa-container hidden-xs"><a href="contact.php" class="btn btn-xs btn-default hexa-btn" data-original-title="{$LANG.contactus}"><i class="fa fa-send-o"></i></a></div>
  <style media="screen" type="text/css">
   {literal}
   .login-wrapper{position:absolute;top:90px;left:0;right:0;text-align:center;}
   .login-wrapper .logo{margin-bottom:45px;position:relative;left:-2px;}
   .login-wrapper .box{box-shadow:0 0 6px 2px rgba(0,0,0,0.1);border-radius:5px;background:rgba(255,255,255,0.65);}
   .login-wrapper .box h6{text-transform:uppercase;color:#3f77a4;padding-top:30px;font-size:18px;font-weight:600;margin:0 0 30px;}
   .login-wrapper .box input[type="text"],.login-wrapper .box input[type="password"]{font-size:15px;border-color:#b2bfc7;}
   .login-wrapper .box input:-moz-placeholder{color:#9ba8b6;font-size:15px;letter-spacing:0;font-style:italic;}
   .login-wrapper .box a.forgot{display:block;text-align:right;font-style:italic;text-decoration:underline;color:#3d88ba;font-size:13px;margin-bottom:6px;}
   .login-wrapper .box .remember{display:block;overflow:hidden;margin-bottom:20px;}
   .login-wrapper .box .remember input[type="checkbox"]{float:left;margin-right:8px;}
   .login-wrapper .box .remember label{float:left;color:#4a576a;font-size:13px;font-weight:600;}
   .login-wrapper .box .login{text-transform:uppercase;font-size:13px;padding:8px 30px;}
   .login-wrapper .no-account{float:none;text-align:center;font-size:14px;margin:25px auto 0;}
   .login-wrapper .no-account p{display:inline-block;color:#fff;}
   .login-wrapper .no-account a{color:#fff;margin-left:7px;border-bottom:1px solid;transition:all .1s linear;-moz-transition:all .1s linear;-webkit-transition:all .1s linear;-o-transition:all .1s linear;}
   .login-wrapper .no-account a:hover{text-decoration:none;color:#fff;border-bottom-color:#fff;}
   .login-wrapper .input-group-addon{background-color:transparent;color:#a2afb7;border-color:#b2bfc7;}
   .logo-page{margin-bottom:30px;}
   .logo{color:#fff;font-size:33px;font-weight:100;}
   .login-wrapper .box input:-ms-input-placeholder,.login-wrapper .box input::-webkit-input-placeholder{color:#9ba8b6;font-style:italic;letter-spacing:0;font-size:15px;}
   .displaynone{display:none!important;}
   .text-alert{margin-top:15px;padding:5px;}
   {/literal}
 </style>
 <div class="login-wrapper">
  <div class="container">
   <div class="row">
    <div class="col-md-4 col-md-offset-4 logo-page"> 
     <a href="index.php" title="{$companyname}"><span class="logo"><span aria-hidden="true" class="icon icon"></span>九龙主机</span></a>
   </div>
 </div>
 {else}
 {$headeroutput}
 <div class="hexa-container hidden-xs"><a href="contact.php" class="btn btn-xs btn-default hexa-btn" data-original-title="{$LANG.contactus}"><i class="fa fa-send-o"></i></a></div>
 <div class="container">
  <div class="row">
    <nav class="navbar" role="navigation">
      <div class="navbar-header">
        <div class="navbar-brand"><a href="index.php"><span class="logo"><span aria-hidden="true" class="icon icon"></span>九龙主机</span></a><i class="fa fa-bars fa-lg btn-nav-toggle-responsive"></i></div>
      </div>
      {if $loggedin}
      <div class="collapse navbar-collapse">
        <ul class="nav navbar-nav user-menu navbar-right" id="user-menu">
          <li><a href="#" class="user dropdown-toggle" data-toggle="dropdown"><span class="username">  {$clientsdetails.firstname} {$clientsdetails.lastname} </span></a>
            <ul class="dropdown-menu">          
              <li><a href="clientarea.php?action=details"><span aria-hidden="true" class="icon icon-user"></span> {$LANG.editaccountdetails}</a></li>
              <li><a href="clientarea.php?action=changepw"><span aria-hidden="true" class="icon icon-lock"></span> {$LANG.clientareanavchangepw}</a></li>
              <li class="divider"></li>
              {if $condlinks.addfunds}<li><a href="clientarea.php?action=addfunds"><span aria-hidden="true" class="icon icon-plus"></span> {$LANG.addfunds}</a></li>{/if}
              {if $condlinks.updatecc}<li><a href="clientarea.php?action=creditcard"><span aria-hidden="true" class="icon icon-credit-card"></span> {$LANG.navmanagecc}</a></li>{/if}
              <li class="divider"></li>
              <li><a href="logout.php"><span aria-hidden="true" class="icon icon-logout"></span> {$LANG.logouttitle}</a></li>
            </ul>
          </li>          
          <li><a href="clientarea.php?action=emails" class="settings"><span aria-hidden="true" class="icon icon-envelope"></span></a></li>
          <li><a href="cart.php?a=view" class="settings"><span aria-hidden="true" class="icon icon-basket"><span class="badge cart bg-success">{php}$cartitems = count ($_SESSION['cart']['products']) + count ($_SESSION['cart']['addons']) + count ($_SESSION['cart']['domains']);echo $cartitems{/php}</span></span></a></li>
          <li><a href="#" class="settings dropdown-toggle" data-toggle="dropdown"><span aria-hidden="true" class="icon icon-bell"></span><span class="badge noti bg-success"></span></a>
            <ul class="dropdown-menu notifications" id="noti">
             {if $clientsstats.numoverdueinvoices>0 AND in_array('invoices',$contactpermissions)}
             <li>
              <a href="clientarea.php?action=masspay&amp;all=true">
                <span aria-hidden="true" class="icon noty-icon bg-danger icon-docs"></span>
                <span class="description">{$LANG.youhaveoverdueinvoices|sprintf2:$clientsstats.numoverdueinvoices}</span>
              </a>
            </li>
            {/if}
            {if !$clientsstats.incredit AND in_array('invoices',$contactpermissions)}
            <li>
              <a href="clientarea.php?action=addfunds">
                <span aria-hidden="true" class="icon noty-icon bg-success icon-wallet"></span>
                <span class="description">{$LANG.availcreditbaldesc|sprintf2:$clientsstats.creditbalance}</span>
              </a>
            </li>
            {/if}
            {if $openissuescount} 
            <li>
              <a href="serverstatus.php?view=open">
                <span aria-hidden="true" class="icon noty-icon bg-warning icon-feed"></span> 
                <span class="description">{$openissuescount} Open Service Issues</span>
              </a>
            </li>
            {/if}
            {if $scheduledissuescount} 
            <li>
              <a href="serverstatus.php?view=scheduled">
                <span aria-hidden="true" class="icon noty-icon bg-warning icon-feed"></span> 
                <span class="description">{$scheduledissuescount} Scheduled Service</span>
              </a>
            </li>
            {/if}
            {if $ccexpiringsoon AND in_array('invoices',$contactpermissions)}
            <li>
             <a href="clientarea.php?action=creditcard">
               <span aria-hidden="true" class="icon noty-icon bg-danger icon-credit-card"></span>
               <span class="description">{$LANG.ccexpiringsoon}</span>
             </a>
           </li>
           {/if}
         </ul>
       </li>
       <li><a href="#" class="settings dropdown-toggle" data-toggle="dropdown"><span aria-hidden="true" class="icon icon-globe"></span></a>
        <ul class="dropdown-menu" id="lang-links" role="menu">
          {php}
          foreach(getValidLanguages() as $lang){
          echo '<li';
          if( isset( $_SESSION['Language'] ) && strtolower( $_SESSION['Language'] ) == $lang || 
          !isset( $_SESSION['Language'] ) && $lang == strtolower( $CONFIG['Language'] ) ){
          echo ' class="active"';
        }
        echo '><a href="#" data-lang="'.ucfirst($lang).'" title="'.ucfirst( $lang ).'">'.ucfirst( $lang ).'</a></li>';
      }
      {/php}
    </ul>
  </li>
  <li><a href="logout.php" class="settings"><span aria-hidden="true" class="icon icon-logout"></span></a></li>
</ul>
</div>
{else}
<div class="collapse navbar-collapse">
  <ul class="nav navbar-nav user-menu navbar-right" id="user-menu">
    <li><a href="login.php"><span aria-hidden="true" class="icon icon-login"></span> {$LANG.login}</a></li>
  </ul>
</div>
{/if}
</nav>
</div>
</div>
{php}
 if($_COOKIE[minibar]=='1') 
 {
 $mini="mini-sidebar";
 $hide="hide";
 $diplay="style='display: none';";
 }else {
 $mini="";
 $hide="";
 $diplay="";
 }
 {/php}
<div class="site-holder container {php}echo $mini{/php}">
  <div class="box-holder">
    <div class="left-sidebar">
      <div class="sidebar-holder" data-original-title="" title="">
        {if $loggedin}
        <ul class="nav  nav-list">
          <li class="nav-toggle"><button class="btn  btn-nav-toggle"><i class="fa toggle-left fa-angle-double-left" style="color:#eee;"></i> </button></li>
          <li {if $filename eq "clientarea" and $smarty.get.action eq ""} class="active"{/if}><a href="clientarea.php" data-original-title="{$LANG.dashboard}"><span aria-hidden="true" class="icon icon-home"></span><span class="hidden-minibar {php}echo $hide{/php} "> {$LANG.dashboard}</span></a></li>
          <li {if $filename eq "cart"} class="active"{/if}><a href="cart.php" data-original-title="{$LANG.ordertitle}"><span aria-hidden="true" class="icon icon-rocket"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.ordertitle}</span></a></li>
          <li class="submenu"><a class="dropdown {if $filename eq "clientarea" and $smarty.get.action eq "details" || $filename eq "clientarea" and $smarty.get.action eq "contacts" || $filename eq "clientarea" and $smarty.get.action eq "creditcard" || $filename eq "clientarea" and $smarty.get.action eq "changepw" || $filename eq "clientarea" and $smarty.get.action eq "security"}active-parent{/if}" href="javascript:;" data-original-title="{$LANG.account}"><span aria-hidden="true" class="icon icon-user"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.account}</span><span class="fa arrow"></span></a>
           <ul {php}echo $display{/php}>
             <li {if $filename eq "clientarea" and $smarty.get.action eq "details"}class="active"{/if}><a href="clientarea.php?action=details" data-original-title="{$LANG.clientareanavdetails}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.clientareanavdetails}</span></a></li>
             {if $condlinks.updatecc}<li {if $filename eq "clientarea" and $smarty.get.action eq "creditcard"}class="active"{/if}><a href="clientarea.php?action=creditcard" data-original-title="{$LANG.navmanagecc}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.navmanagecc}</span></a></li>{/if}
             <li {if $filename eq "clientarea" and $smarty.get.action eq "contacts"}class="active"{/if}><a href="clientarea.php?action=contacts" data-original-title="{$LANG.clientareanavcontacts}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.clientareanavcontacts}</span></a></li>
             <li {if $filename eq "clientarea" and $smarty.get.action eq "changepw"}class="active"{/if}><a href="clientarea.php?action=changepw" data-original-title="{$LANG.clientareanavchangepw}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.clientareanavchangepw}</span></a></li>
             <li {if $filename eq "clientarea" and $smarty.get.action eq "security"}class="active"{/if}><a href="clientarea.php?action=security" data-original-title="{$LANG.clientareanavsecurity}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.clientareanavsecurity}</span></a></li>
           </ul>
         </li> 
         <li {if $filename eq "clientarea" and $smarty.get.action eq "domains"} class="active"{/if}><a href="clientarea.php?action=domains" data-original-title="{$LANG.cartproductdomain}"><span aria-hidden="true" class="icon icon-globe"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.cartproductdomain}</span><span class="badge badge-default pull-right">{$clientsstats.numactivedomains}</span></a></li>
         <li {if $filename eq "clientarea" and $smarty.get.action eq "products"} class="active"{/if}><a href="clientarea.php?action=products" data-original-title="{$LANG.navservices}"><span aria-hidden="true" class="icon icon-layers"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.navservices}</span><span class="badge badge-default pull-right">{$clientsstats.productsnumactive}</span></a></li>        
         <li {if $filename eq "clientarea" and $smarty.get.action eq "invoices"} class="active"{/if}><a href="clientarea.php?action=invoices" data-original-title="{$LANG.invoices}"><span aria-hidden="true" class="icon icon-drawer"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.invoices}</span></a></li>
         <li {if $filename eq "clientarea" and $smarty.get.action eq "quotes"} class="active"{/if}><a href="clientarea.php?action=quotes" data-original-title="{$LANG.quotestitle}"><span aria-hidden="true" class="icon icon-calculator"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.quotestitle}</span></a></li>
         <li class="submenu"><a class="dropdown {if $filename eq "submitticket" || $filename eq "supporttickets" || $filename eq "serverstatus" || $filename eq "downloads" || $filename eq "knowledgebase"}active-parent{/if}" href="javascript:;" data-original-title="{$LANG.navsupport}"><span aria-hidden="true" class="icon icon-support"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.navsupport}</span><span class="fa arrow"></span></a>
           <ul {php}echo $display{/php}>
            <li {if $filename eq "submitticket"} class="active"{/if}><a href="submitticket.php" data-original-title="{$LANG.opennewticket}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.opennewticket}</span></a></li>  
            <li {if $filename eq "supporttickets"} class="active"{/if}><a href="supporttickets.php" data-original-title="{$LANG.supportticketspagetitle}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.supportticketspagetitle}</span><span class="badge pull-right">{$clientsstats.numactivetickets}</span></a></li>
            <li {if $filename eq "serverstatus"} class="active"{/if}><a href="serverstatus.php" data-original-title="{$LANG.serverstatustitle}"><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.serverstatustitle}</span></a></li>  
            <li {if $filename eq "downloads"} class="active"{/if}><a href="downloads.php" data-original-title="{$LANG.downloadstitle}"><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.downloadstitle}</span></a></li>
            <li {if $filename eq "knowledgebase"} class="active"{/if}><a href="knowledgebase.php" data-original-title="{$LANG.knowledgebasetitle}"><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.knowledgebasetitle}</span></a></li>
			<li class="info-aa"{if $filename eq "likeenter.com"} class="active"{/if}><a href="http://likeenter.com">likeenter.com</a></li>
          </ul>
        </li>
        {if $condlinks.affiliates} <li {if $filename eq "affiliates"} class="active"{/if}><a href="affiliates.php" data-original-title="{$LANG.affiliatestitle}"><span aria-hidden="true" class="icon icon-users"></span><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.affiliatestitle}</span></a></li>{/if}
      </ul>
      {else}
      <ul class="nav nav-list">      
        <li class="nav-toggle"><button class="btn  btn-nav-toggle"><i class="fa toggle-left fa-angle-double-left" style="color:#eee;"></i> </button></li>
        <li {if $filename eq "cart"} class="active"{/if}><a href="cart.php" data-original-title="{$LANG.ordertitle}"><span aria-hidden="true" class="icon icon-rocket"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.ordertitle}</span></a></li>
        <li {if $filename eq "supporttickets"} class="active"{/if}><a href="serverstatus.php" data-original-title="{$LANG.serverstatustitle}"><span aria-hidden="true" class="icon icon-feed"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.serverstatustitle}</span></a></li>
        <li {if $filename eq "contact"} class="active"{/if}><a href="contact.php" data-original-title="{$LANG.contactus}"><span aria-hidden="true" class="icon icon-envelope"></span><span class="hidden-minibar {php}echo $hide{/php}"> {$LANG.contactus}</span></a></li>
        {if $condlinks.affiliates} <li {if $filename eq "affiliates"} class="active"{/if}><a href="affiliates.php" data-original-title="{$LANG.affiliatestitle}"><span aria-hidden="true" class="icon icon-users"></span><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.affiliatestitle}</span></a></li>{/if}
        <li {if $filename eq "downloads"} class="active"{/if}><a href="downloads.php" data-original-title="{$LANG.downloadstitle}"><span aria-hidden="true" class="icon icon-cloud-download"></span><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.downloadstitle}</span></a></li>
        <li {if $filename eq "knowledgebase"} class="active"{/if}><a href="knowledgebase.php" data-original-title="{$LANG.knowledgebasetitle}"><span aria-hidden="true" class="icon icon-question"></span><span class="hidden-minibar {php}echo $hide{/php}">{$LANG.knowledgebasetitle}</span></a></li>
      </ul>
      {/if}
    </div>
  </div>    
  <div class="content onload">
    <div class="row">   
      <div class="col-md-12">
        {/if}