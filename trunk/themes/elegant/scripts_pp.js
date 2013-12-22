﻿(function() {
  var session_storage = window.sessionStorage || {};

  var menubar=jQuery("#menubar"),
      menuswitcher,
      content=jQuery("#the_page > .content"),
      pcontent=jQuery("#content"),
      imageInfos=jQuery("#imageInfos"),
      infoswitcher,
      theImage=jQuery("#theImage"),
      comments=jQuery("#thePicturePage #comments"),
      comments_button,
      commentsswitcher,
      comments_add,
      comments_top_offset = 0;

  if (session_storage['picture-menu'] == 'visible') {
    jQuery("head").append('<style>#content.contentWithMenu, #the_page > .content {margin-left:240px;}</style>');
  }
  else {
    jQuery("head").append('<style>#the_page #menubar {display:none;} #content.contentWithMenu, #the_page > .content {margin-left:35px;}</style>');
  }

  function hideMenu(delay) {
    menubar.hide(delay);
    menuswitcher.addClass("menuhidden").removeClass("menushown");
    content.addClass("menuhidden").removeClass("menushown");
    pcontent.addClass("menuhidden").removeClass("menushown");
    session_storage['picture-menu'] = 'hidden';
  }

  function showMenu(delay) {
    menubar.show(delay);
    menuswitcher.addClass("menushown").removeClass("menuhidden");
    content.addClass("menushown").removeClass("menuhidden");
    pcontent.addClass("menushown").removeClass("menuhidden");
    session_storage['picture-menu'] = 'visible';
  }

  function hideInfo(delay) {
    imageInfos.hide(delay);
    infoswitcher.addClass("infohidden").removeClass("infoshown");
    theImage.addClass("infohidden").removeClass("infoshown");
    session_storage['side-info'] = 'hidden';
  }

  function showInfo(delay) {
    imageInfos.show(delay);
    infoswitcher.addClass("infoshown").removeClass("infohidden");
    theImage.addClass("infoshown").removeClass("infohidden");
    session_storage['side-info'] = 'visible';
  }

  function commentsToggle() {
    if (comments.hasClass("commentshidden")) {
        comments.removeClass("commentshidden").addClass("commentsshown");
        comments_button.addClass("comments_toggle_off").removeClass("comments_toggle_on");;
        session_storage['comments'] = 'visible';

        comments_top_offset = comments_add.offset().top - parseFloat(comments_add.css('marginTop').replace(/auto/, 0));
      }
      else {
        comments.addClass("commentshidden").removeClass("commentsshown");
        comments_button.addClass("comments_toggle_on").removeClass("comments_toggle_off");;
        session_storage['comments'] = 'hidden';
      }
  }

  jQuery(function(){
    // side-menu show/hide
    if (menubar.length == 1 && p_main_menu!="disabled") {
      menuswitcher=jQuery("#menuSwitcher");

      menuswitcher.html('<div class="switchArrow">&nbsp;</div>');

      if (session_storage['picture-menu'] == 'hidden' || p_main_menu == 'off') {
        hideMenu(0);
      }
      else {
        showMenu(0);
      }

      menuswitcher.click(function(e){
        if (menubar.is(":hidden")) {
          showMenu(0);
        }
        else {
          hideMenu(0);
        }
        e.preventDefault();
      });
    }

    // info show/hide
    if (imageInfos.length == 1 && p_pict_descr!="disabled") {
      infoswitcher=jQuery("#infoSwitcher");

      infoswitcher.html('<div class="switchArrow">&nbsp;</div>');

      if (session_storage['side-info'] == 'hidden' || p_pict_descr == 'off') {
        hideInfo(0);
      }
      else {
        showInfo(0);
      }

      infoswitcher.click(function(e){
        if (imageInfos.is(":hidden")) {
          showInfo(0);
        }
        else {
          hideInfo(0);
        }
        e.preventDefault();
      });
    }

    // comments show/hide
    if (comments.length == 1 && p_pict_comment!="disabled") {
      commentsswitcher=jQuery("#commentsSwitcher");
      comments_button=jQuery("#comments h3");
      comments_add=jQuery('#commentAdd');

      commentsswitcher.html('<div class="switchArrow">&nbsp;</div>');

      if (comments_button.length == 0) {
        jQuery("#addComment").before("<h3>Comments</h3>");
        comments_button=jQuery("#comments h3");
      }

      if (session_storage['comments'] == 'hidden' || p_pict_comment == 'off') {
        comments.addClass("commentshidden");
        comments_button.addClass("comments_toggle comments_toggle_on");
      }
      else {
        comments.addClass("commentsshown");
        comments_button.addClass("comments_toggle comments_toggle_off");
      }

      comments_button.click(commentsToggle);
      commentsswitcher.click(commentsToggle);

      jQuery(window).scroll(function (event) {
        if (comments_top_offset==0) return;

        // what the y position of the scroll is
        var y = jQuery(this).scrollTop();

        // whether that's below the form
        if (y >= comments_top_offset) {
          // if so, ad the fixed class
          comments_add.addClass('fixed');
        }
        else {
          // otherwise remove it
          comments_add.removeClass('fixed');
        }
      });

      if (comments_add.is(":visible")) {
        comments_top_offset = comments_add.offset().top - parseFloat(comments_add.css('marginTop').replace(/auto/, 0));
      }
    }
  });
}());