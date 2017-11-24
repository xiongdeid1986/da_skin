
        </div><!-- /.main-content -->
        {if !$inShoppingCart && $secondarySidebar->hasChildren()}
            <div class="col-md-3 pull-md-left sidebar">
                {include file="$template/includes/sidebar.tpl" sidebar=$secondarySidebar}
            </div>
        {/if}
    </div>
    <div class="clearfix"></div>
</section>


   <!-- FOOTER -->
    <footer class="footer">
        <div class="container ">
            <div class="row">
                <div class="footer-entry col-md-3">
                    <h3 class="title">NRGHost</h3>
                    <div class="text">Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit. Duis posuere blandit orci sed tincidunt. Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit. Praesent nisl sapien, semper quis convallis et, tempus vitae dolor.</div>
                </div>
                <div class="footer-entry col-md-2 col-sm-3 col-xs-6">
                    <h3 class="title">NRGHost</h3>
                    <ul>
                        <li><a href="hosting.html">Web Hosting</a></li>
                        <li><a href="hosting.html">Reseller Hosting</a></li>
                        <li><a href="host-vps.html">VPS Hosting</a></li>
                        <li><a href="host-dedicated.html">Dedicated Hosting</a></li>
                        <li><a href="hosting.html">Application Hosting</a></li>
                        <li><a href="hosting.html">Windows Hosting</a></li>
                    </ul>
                </div>
                <div class="footer-entry col-md-2 col-sm-3 col-xs-6">
                    <h3 class="title">Company</h3>
                    <ul>
                        <li><a href="host-about.html">About NRGHost</a></li>
                        <li><a href="host-testimonials.html">Awards &amp; Reviews</a></li>
                        <li><a href="host-about.html">Press &amp; Media</a></li>
                        <li><a href="blog.html">Company Blog</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                        <li><a href="host-about.html">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-entry col-md-5 col-sm-6 col-xs-12">
                    <h3 class="title">Newsletter Subscribe</h3>
                    <div class="text">Curabitur porttitor nisi ac nunc ornare, in fringilla nisl blandit. Sed quam metus, faucibus et pulvinar ut, volutpat ut orci. Integer sollicitudin cursus massa, non laoreet risus pretium eget.</div>
                    <div class="subscription-form">
                        <form>
                            <input type="email" required value="" placeholder="Your Email..."/>
                            <input type="submit" value="" />
                        </form>
                    </div>
                </div>
            </div>
            <div class="row nopadding social-icons-wrapper">
                <div class="col-xs-3 nopadding">
                    <a class="social-icon" href="https://www.facebook.com/" target="_blank" style="background-color: #3b5998;">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-17.png" alt="" />
                    </a>
                </div>
                <div class="col-xs-3 nopadding">
                    <a class="social-icon" href="https://plus.google.com" target="_blank" style="background-color: #e02f2f;">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-18.png" alt="" />
                    </a>
                </div>
                <div class="col-xs-3 nopadding">
                    <a class="social-icon" href="https://twitter.com/" target="_blank" style="background-color: #55acee;">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-19.png" alt="" />
                    </a>
                </div>
                <div class="col-xs-3 nopadding">
                    <a class="social-icon" href="https://www.linkedin.com/" target="_blank" style="background-color: #007bb5;">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-20.png" alt="" />
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <ul class="footer-menu">
                        <li><a class="active" href="index.html">Home</a></li>
                        <li><a href="hosting.html">Hosting</a></li>
                        <li><a href="host-domains.html">Domains</a></li>
                        <li><a href="host-about.html">Pages</a></li>
                        <li><a href="blog.html">Blog</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="copyright">&copy; {$date_year} All rights reserved. {$comapnyname}</div>
                </div>
            </div>
        </div>
        <div class="footer-line">
            <div class="container">
                <div class="row">
                    <div class="footer-line-entry col-md-3 col-sm-6 col-xs-12">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-22.png" alt=""/>
                        <div class="content">
                            <div class="cell-view">24/7 Custtomer Support</div>
                        </div>
                    </div>
                    <div class="footer-line-entry col-md-3 col-sm-6 col-xs-12">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-23.png" alt=""/>
                        <div class="content">
                            <div class="cell-view"><a href="mailto:support@nrghost.com">support@nrghost.com</a></div>
                        </div>
                    </div>
                    <div class="footer-line-entry col-md-3 col-sm-6 col-xs-12">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-24.png" alt=""/>
                        <div class="content">
                            <div class="cell-view"><a href="tel:+48 555 8753 005">+48 555 8753 005</a></div>
                        </div>
                    </div>
                    <div class="footer-line-entry col-md-3 col-sm-6 col-xs-12">
                        <img src="templates/{$template}/assets/nrghosts/img/icon-25.png" alt=""/>
                        <div class="content">
                            <div class="cell-view"><a href="#">Live Chat</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

<script src="{$BASE_PATH_JS}/bootstrap.min.js"></script>
<script src="{$BASE_PATH_JS}/jquery-ui.min.js"></script>
<script type="text/javascript">
    var csrfToken = '{$token}';
</script>
<script src="{$WEB_ROOT}/templates/{$template}/js/whmcs.js"></script>

{$footeroutput}

    <script src="templates/{$template}/assets/nrghosts/js/idangerous.swiper.min.js"></script>
    <script src="templates/{$template}/assets/nrghosts/js/global.js"></script>
    <script src="templates/{$template}/assets/nrghosts/js/wow.min.js"></script>
    <script>
        var wow = new WOW(
            {
                boxClass:     'wow',      // animated element css class (default is wow)
                animateClass: 'animated', // animation css class (default is animated)
                offset:       100,          // distance to the element when triggering the animation (default is 0)
                mobile:       true,       // trigger animations on mobile devices (default is true)
                live:         true,       // act on asynchronously loaded content (default is true)
                callback:     function(box) {
                  // the callback is fired every time an animation is started
                  // the argument that is passed in is the DOM node being animated
                }
            }
        );
        wow.init();
    </script>

    {$footeroutput}

</body>
</html>
