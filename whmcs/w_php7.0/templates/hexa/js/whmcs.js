(function(j) {
  j.fn.extend({
    accordion: function() {
      return this.each(function() {
        function b(c, b) {
          $(c).parent(d).siblings().removeClass(e).children(f).slideUp(g);
          $(c).siblings(f)[b || h](b == "show" ? g : !1, function() {
            $(c).siblings(f).is(":visible") ? $(c).parents(d).not(a.parents()).addClass(e) : $(c).parent(d).removeClass(e);
            b == "show" && $(c).parents(d).not(a.parents()).addClass(e);
            $(c).parents().show()
          })
        }
        var a = $(this), e = "active", h = "slideToggle", f = "ul, div", g = "fast", d = "li";
        if (a.data("accordiated"))
          return !1;
        $.each(a.find("ul, li>div"),
          function() {
            $(this).data("accordiated", !0);
            $(this).hide()
          });
        $.each(a.find("a"), function() {
          $(this).click(function() {
            b(this, h)
          });
          $(this).bind("activate-node", function() {
            a.find(f).not($(this).parents()).not($(this).siblings()).slideUp(g);
            b(this, "slideDown")
          })
        });
        var i = location.hash ? a.find("a[href=" + location.hash + "]")[0]: a.find("li.current a")[0];
        i && b(i, !1)
      })
}
})
})(jQuery);
jQuery(document).ready(function(){
  // Tabs Changer
  // ===============================
  //Default Action  
  jQuery(".tab-content").hide(); //Hide all content
  if (jQuery(location).attr('hash').substr(1)!="") {
    var activeTab = jQuery(location).attr('hash');
    jQuery("ul").find('li').removeClass('open');
    jQuery("ul.nav li").removeClass("active"); //Remove any "active" class
    jQuery(activeTab+"nav").addClass("active");
    jQuery(activeTab).show();
  } else {
    jQuery("#tabs ul.nav .nav-tabs li:first").addClass("active").show(); //Activate first tab
    jQuery(".tab-content:first").show(); //Show first tab content 
  }
  //On Click Event
  jQuery("#tabs ul.nav li").click(function() {
    jQuery("ul").find('li').removeClass('open');
    jQuery("ul.nav li").removeClass("active"); //Remove any "active" class
    jQuery(this).addClass("active"); //Add "active" class to selected tab
    var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
        if (activeTab.substr(0,1)=="#" && activeTab.substr(1)!="") { //Determine if a tab or link
            jQuery(".tab-content").hide(); //Hide all tab content
        jQuery(activeTab).fadeIn(); //Fade in the active content
        return false;
      } else {
            return true; // If link allow redirect
          }
        });
});
$('div#languagechooser').find('select').addClass('form-control');
// Checkboxes Toggle
// ===============================
function toggleCheckboxes(classname) {
  jQuery("."+classname).attr('checked',!jQuery("."+classname+":first").is(':checked'));
}
// Disable Field Class
// ===============================
function disableFields(classname,disable) {
  if (disable) jQuery("."+classname).attr("disabled","disabled");
  else jQuery("."+classname).removeAttr("disabled");
}
// Open Centered Popup
// ===============================
function popupWindow(addr,popname,w,h,features) {
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  if (winl < 0) winl = 0;
  if (wint < 0) wint = 0;
  var settings = 'height=' + h + ',';
  settings += 'width=' + w + ',';
  settings += 'top=' + wint + ',';
  settings += 'left=' + winl + ',';
  settings += features;
  win = window.open(addr,popname,settings);
  win.window.focus();
}
// Support Tickets
// ===============================
function extraTicketAttachment() {
  jQuery("#fileuploads").append('<p><div class="input-group"><span class="input-group-btn"><span class="btn btn-default btn-sm btn-file"><span class="glyphicon glyphicon-folder-open"></span> <input type="file" name="attachments[]" multiple=""></span></span><input type="text" class="form-control input-sm" readonly=""></div></p>');
  $(document).ready( function() {
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {

      var input = $(this).parents('.input-group').find(':text'),
      log = numFiles > 1 ? numFiles + ' files selected' : label;

      if( input.length ) {
        input.val(log);
      } else {
        if( log ) alert(log);
      }

    });
  });   
}
function rating_hover(id) {
  var selrating=id.split('_');
  for(var i=1; i<=5; i++){
    if(i<=selrating[1]) document.getElementById(selrating[0]+'_'+i).style.background="url(images/rating_pos.png)";
    if(i>selrating[1]) document.getElementById(selrating[0]+'_'+i).style.background="url(images/rating_neg.png)";
  }
}
function rating_leave(id){
  for(var i=1; i<=5; i++){
    document.getElementById(id+'_'+i).style.background="url(images/rating_neg.png)";
  }
}
function rating_select(tid,c,id){
  window.location='viewticket.php?tid='+tid+'&c='+c+'&rating='+id;
}
// Sidebar
// ===============================
$('ul.nav-list').accordion();
$('.site-holder.container .nav > li > ul > li.active').parent().css('display','block');    
$('.site-holder.container.mini-sidebar .nav > li > ul > li.active').parent().css('display','none');  
$('.btn-nav-toggle-responsive').click(function(){
  $('.left-sidebar').toggleClass('show-fullsidebar');  
});
$('li.nav-toggle > button').click(function(e){
        //Set cookie
        if($.cookie('minibar')==null||$.cookie('minibar')==0) $.cookie('minibar',1);
        else {
          $.cookie('minibar',0);
        }
        e.preventDefault();
        changeSidebarState();
      });
    //Load sidebar state
    $(function(){
      if($.cookie('minibar')==1) {
        changeSidebarStateNoAnimate();
      }
    });
    function changeSidebarState(){
      $('.hidden-minibar').toggleClass("hide");
      $('.site-holder').toggleClass("mini-sidebar");
      if($('.toggle-left').hasClass('fa-angle-double-left')){ $('.toggle-left').removeClass('fa-angle-double-left').addClass('fa-angle-double-right'); }
      else { $('.toggle-left').removeClass('fa-angle-double-right').addClass('fa-angle-double-left'); }
      if($('.site-holder').hasClass('mini-sidebar'))
      {    
        $('.sidebar-holder').tooltip({
          selector: "a",
          container: "body",
          placement: "right"
        });
        $('li.submenu ul').tooltip('destroy');
      }
      else
      {
        $('.sidebar-holder').tooltip('destroy');
      }
    }
    function changeSidebarStateNoAnimate(){
      $('.toggle-left').removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
      if($('.site-holder').hasClass('mini-sidebar'))
      {    
        $('.sidebar-holder').tooltip({
          selector: "a",
          container: "body",
          placement: "right"
        });
        $('li.submenu ul').tooltip('destroy');
      }
      else
      {
        $('.sidebar-holder').tooltip('destroy');
      }      
    }
      //
      if($('.site-holder').hasClass('mini-sidebar'))
      {    
        $('.sidebar-holder').tooltip({
          selector: "a",
          container: "body",
          placement: "right"
        });
        $('li.submenu').tooltip('destroy');
      }
      else
      {
        $('.sidebar-holder').tooltip('destroy');
      }
      $('.show-info').click(function(){
        $('.page-information').toggleClass('hidden');
      });

      $('.site-holder.mini-sidebar .content').click(function () { 
       $('.site-holder.mini-sidebar li.submenu ul').hide();
       $('.site-holder.mini-sidebar li.submenu').removeClass('active');

     });

// Count Active Notifications
// ===============================
$(function() { 
  $(".noti").text($("#noti li").length)
});

// Language Dropdown
// ===============================
!function(e) {
  e(function() {
    if (e("#lang-links").length > 0) {
      e("#lang-links a").click(function() {
        e('#languagefrm option:contains("' + e(this).attr("data-lang") + '")').prop("selected", true);
        e("#languagefrm").submit();
        return false
      })
    }
  })
}(window.jQuery);
$('.settings-toggle').click(function(e){
  e.preventDefault();
  $('.right-sidebar').toggleClass('right-sidebar-hidden');
});
$('.theme-panel-close').click(function(){
  $('.right-sidebar').toggleClass('right-sidebar-hidden');
});
$(function(){
  $('.right-sidebar-holder').slimScroll({
    alwaysVisible: true,        
    height: '390px'
  });
});