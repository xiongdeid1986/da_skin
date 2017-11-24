<?php /* Smarty version 2.6.28, created on 2016-12-13 17:28:33
         compiled from webhoster/footer.tpl */ ?>
		

		<?php if ($this->_tpl_vars['filename'] == index && ( $_GET['search'] == "" ) && ( $_GET['action'] == "" ) || $this->_tpl_vars['filename'] == 'web_hosting' || $this->_tpl_vars['filename'] == 'web_hosting_windows' || $this->_tpl_vars['filename'] == 'reseller_hosting' || $this->_tpl_vars['filename'] == 'vps_hosting' || $this->_tpl_vars['filename'] == 'dedicated_servers'): ?><?php else: ?>
			</div>
		</div>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['filename'] == index && ( $_GET['search'] == "" ) && ( $_GET['action'] == "" )): ?><?php else: ?>
		<div class="block-s1 bg-primary">
			<div class="container hidden-xs">
				<div class="pull-left">
					<h3>有什么问题?不要犹豫，请马上告诉我们！</h3>
				</div>
				<div class="pull-right">
					<p><a href="submitticket.php?step=2&amp;deptid=1" class="btn btn-inverse"><i class="fa fa-comments"></i>在线提交工单</a></p>					
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="container visible-xs">
				<div class="text-center">
					<h3>有什么问题?不要犹豫，请马上告诉我们！</h3>
					<p><a href="submitticket.php?step=2&amp;deptid=1" class="btn btn-inverse"><i class="fa fa-comments"></i>在线提交工单</a></p>						
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="block-s3 light">
			<div class="container">
				<div class="space-12"></div>
				<div class="owl-carousel partners-slider">
					<!-- Wrapper for partners slides --> 
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/WHMCS.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/solusvm_large.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/logo-cpanel.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/logo_rapidssl.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/geotrust.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/Comodo-partner.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/directadmin.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/kloxo.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/Webmin.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/thawte.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/certum.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/symantec.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<div class="owl-item">
						<img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/foot/rc-logo.png" alt="Owl Image" class="center-block img-responsive">
					</div>
					<!-- /Wrapper for slides -->
				</div>
			</div>
		</div>	
		
		<div class="block-breadcrumbs">
			<div class="container">
				<ol class="breadcrumb">
					<li><i class="fa fa-map-marker text-primary"></i></li>
					<li><?php $nav = explode(" > ",$this->get_template_vars('breadcrumbnav'));foreach ($nav as $links) {if(end($nav) == $links){ echo strip_tags($links);}else {echo $links." <i class='fa fa-angle-double-right'></i> ";}} ?></li>
				</ol>
			
			</div>
		</div>
		
		<!--Footer-->
		<div class="block-footer">
			<div class="container">
				<div class="row hidden-xs hidden-sm">
					<div class="col-md-2 col-sm-6">
						<h4>站长工具</h4>
						<ul class="list-unstyled">
       								<li><a href="http://seo.chinaz.com">SEO查询</a></li>
       								<li><a href="http://whois.chinaz.com">Whois查询</a></li>
								<li><a href="http://tool.chinaz.com/beian.aspx">备案查询</a></li>
								<li><a href="http://ce.cloud.360.cn">奇云测</a></li>
						</ul>
					</div>

					<div class="col-md-2 col-sm-6">
						<h4>产品服务</h4>
						<ul class="list-unstyled">
       								<li><a href="cart.php?gid=6">香港免备案主机</a></li>
								<li><a href="cart.php?gid=2">美国内华达主机</a></li>
								<li><a href="cart.php?gid=3">美国佛里蒙特主机</a></li>
							
						</ul>
					</div>

					<div class="col-md-2 col-sm-6">
						<h4>快速链接</h4>
						<ul class="list-unstyled">
       								<li><a href="knowledgebase.php">帮助中心</a></li>
								<li><a href="downloads.php">相关下载</a></li>
								<li><a href="affiliates.php">推介计划</a></li>
								<li><a href="cart.php">购买服务</a></li>
						</ul>
					</div>


					<div class="col-md-2 col-sm-6">
						<h4>关于我们</h4>
						<ul class="list-unstyled">
							<li><a href="http://www.ddweb.com.cn"><i class="fa fa-angle-double-right"></i> 关于我们</a></li>
							<li><a href="announcements.php?id=1"><i class="fa fa-angle-double-right"></i>用户条款</a></li>
						</ul>
						
					</div>

					<div class="col-md-2 col-sm-6">
						<h4>友情链接</h4>
						<ul class="list-unstyled">
							<li><a href="http://fanhuahost.com"><i class="fa fa-angle-double-right"></i>繁华互联</a></li>
							<li><a href="http://www.51mf.top"><i class="fa fa-angle-double-right"></i>我要免费部落</a></li>							
						</ul>
					</div>

					<div class="col-md-2 social">
						<h4>联系方式</h4>
						<ul class="list-inline">
							<li><a href="http://www.ddweb.com.cn/" class="btn btn-circle btn-facebook btn-xs"><i class="fa fa-facebook icon-only"></i></a></li>
							<li><a href="http://www.ddweb.com.cn/" class="btn btn-circle btn-googleplus btn-xs"><i class="fa fa-google-plus icon-only"></i></a></li>
							<li><a href="http://www.ddweb.com.cn/" class="btn btn-circle btn-twitter btn-xs"><i class="fa fa-twitter icon-only"></i></a></li>
							<li><a href="http://www.ddweb.com.cn/" class="btn btn-circle btn-dribbble btn-xs"><i class="fa fa-dribbble icon-only"></i></a></li>
							<li><a href="http://www.ddweb.com.cn/" class="btn btn-circle btn-pinterest btn-xs"><i class="fa fa-pinterest icon-only"></i></a></li>
						</ul>

						<ul class="list-inline">
							<li><i class="fa fa-phone"></i> 400-100-5392</li>
							<li><i class="fa fa-envelope"></i> service@ddweb.com.cn</li>
						</ul>
						
						<!--<a href="http://www.51mf.top" class="btn btn-success">站长博客</a>-->
						
					</div>



				<div class="row visible-xs visible-sm">
					<div class="col-sm-12 social text-center">
						<ul class="list-inline">
							<li><a href="#" class="btn btn-circle btn-facebook btn-xs"><i class="fa fa-facebook icon-only"></i></a></li>
							<li><a href="#" class="btn btn-circle btn-googleplus btn-xs"><i class="fa fa-google-plus icon-only"></i></a></li>
							<li><a href="#" class="btn btn-circle btn-twitter btn-xs"><i class="fa fa-twitter icon-only"></i></a></li>
							<li><a href="#" class="btn btn-circle btn-linkedin btn-xs"><i class="fa fa-linkedin icon-only"></i></a></li>
						</ul>
					</div>
				</div>
				
				
				<div class="additional-info hidden-xs hidden-sm">
					<hr class="separator"/>
				
					<div class="row">
						<div class="col-sm-6">
							<ul class="list-inline">
								<li><a href="#"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/images/awards/award-img1.png"></a></li>
								<li><a href="#"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/images/awards/award-img2.png"></a></li>
								<li><a href="#"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/images/awards/award-img3.png"></a></li>
								<li><a href="#"><img src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/images/awards/award-img4.png"></a></li>
							</ul>
						</div>
						
						<div class="col-sm-6">
							<ul class="list-inline pull-left">
								<li><i class="fa fa-cc-mastercard fa-3x"></i></li>
								<li><i class="fa fa-cc-visa fa-3x"></i></li>
								<li><i class="fa fa-cc-amex fa-3x"></i></li>
								<li><i class="fa fa-cc-discover fa-3x"></i></li>
								<li><i class="fa fa-cc-paypal fa-3x"></i></li>
								<li><i class="fa fa-google-wallet fa-3x"></i></li>
							</ul>
						</div>
					</div>

				</div>

							<div class="row copyright">
					<div class="col-md-6 col-xs-12">
						<p><?php echo $this->_tpl_vars['LANG']['copyright']; ?>
 &copy; <?php echo $this->_tpl_vars['date_year']; ?>
 <?php echo $this->_tpl_vars['companyname']; ?>
. <span class="hidden-xs"><?php echo $this->_tpl_vars['LANG']['allrightsreserved']; ?>
.</span></p>
					</div>
					<div class="col-md-6 col-xs-12">
						<ul class="list-inline">
							<li><a href="">其他链接</a></li>
						</ul>						
					</div>
				</div>
				
				
			</div>
		</div>
		
		<a id="back-to-top" href="#" class="btn btn-primary btn-sm back-to-top" role="button"><i class="fa fa-angle-double-up icon-only bigger-110"></i></a>
		<!--End Footer-->

	</div><!-- /page container -->
	
	<!-- basic scripts -->		
		<script src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/main.js"></script>		
		<script type="text/javascript" src="templates/<?php echo $this->_tpl_vars['template']; ?>
/assets/js/plugins/owl-carousel/owl.carousel.min.js"></script><!-- slider for products -->
		
		<?php echo '<script type="text/javascript">
		// init variables require for all front pages
			Apps.initNavTopBar();
		// end
		
		$(".features-slider").owlCarousel({ 
			autoPlay: 3000, //Set AutoPlay to 3 seconds
			pagination: true,
			items : 3
		});
		
		$(".partners-slider").owlCarousel({ 
			autoPlay: 3000, //Set AutoPlay to 3 seconds
			pagination: false,
			items : 7
		});
		
		</script>'; ?>

	<?php echo $this->_tpl_vars['footeroutput']; ?>
	
<script type="text/javascript">
var __lc = ;
__lc.license = 6072211;

(function() )();
</script>
	</body>
</html>