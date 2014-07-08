{footer_script require='jquery.bootstrap-tour'}{literal}

var tour = new Tour({
  name: "privacy",
  orphan: true,
  onEnd: function (tour) {window.location = "admin.php?tour_ended=privacy";},
  template: "<div class='popover tour'>
  <div class='arrow'></div>
  <h3 class='popover-title'></h3>
  <div class='popover-content'></div>
  <div class='popover-navigation'>
      <button class='btn btn-default' data-role='prev'>� {/literal}{'Prev'|@translate|@escape:'javascript'}{literal}</button>
      <span data-role='separator'>|</span>
      <button class='btn btn-default' data-role='next'>{/literal}{'Next '|@translate|@escape:'javascript'}{literal} �</button>
  </div>
  <button class='btn btn-default' data-role='end'>{/literal}{'End tour'|@translate|@escape:'javascript'}{literal}</button>
  </nav>
</div>",
});
{/literal}{if $TAT_restart}tour.restart();{/if}{literal}

tour.addSteps([
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    title: "{/literal}{'privacy_title1'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp1'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php",
    placement: "bottom",
    element: ".icon-help-circled",
    title: "{/literal}{'privacy_title2'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp2'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=permissions",
    placement: "top",
    element: "#helpContent",
    title: "{/literal}{'privacy_title3'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp3'|@translate|@escape:'javascript'}{literal}",
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=permissions",
    placement: "top",
    element: "#helpContent",
    title: "{/literal}{'privacy_title4'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp4'|@translate|@escape:'javascript'}{literal}"
  },
  {//5
    path: "{/literal}{$TAT_path}{literal}admin.php?page=help&section=groups",
    placement: "top",
    element: "#uploadify",
    title: "{/literal}{'privacy_title5'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp5'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "left",
    element: "#fileQueue",
    title: "{/literal}{'privacy_title6'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp6'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "top",
    element: "#photosAddContent legend",
    title: "{/literal}{'privacy_title7'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp7'|@translate|@escape:'javascript'}{literal}",
    prev:4
  },
  {
    path: /admin\.php\?page=photos_add/,
    redirect:function (tour) {window.location = "admin.php?page=photos_add";},
    placement: "bottom",
    element: "#batchLink",
    reflex:true,
    title: "{/literal}{'privacy_title8'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp8'|@translate|@escape:'javascript'}{literal}",
    prev:4
  },
  {
    path: /admin\.php\?page=(photos_add|batch_manager&filter=prefilter-last_import|prefilter-caddie)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title9'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp9'|@translate|@escape:'javascript'}{literal}"
  },
  {//10
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "right",
    element: ".icon-flag",
    title: "{/literal}{'privacy_title10'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp10'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "left",
    element: "#checkActions",
    title: "{/literal}{'privacy_title11'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp11'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#action",
    title: "{/literal}{'privacy_title12'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp12'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "bottom",
    element: "#tabsheet .normal_tab",
    title: "{/literal}{'privacy_title13'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp13'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=batch_manager&filter=(prefilter-caddie|prefilter-last_import)/,
    redirect:function (tour) {window.location = "admin.php?page=batch_manager&filter=prefilter-last_import";},
    placement: "top",
    element: "#TAT_FC_14",
    reflex:true,
    title: "{/literal}{'privacy_title14'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp14'|@translate|@escape:'javascript'}{literal}",
    onNext:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";}
  },
  {//15
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "bottom",
    element: ".selected_tab",
    title: "{/literal}{'privacy_title15'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp15'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_16",
    title: "{/literal}{'privacy_title16'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp16'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_17",
    title: "{/literal}{'privacy_title17'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp17'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=photo-/,
    redirect:function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},
    placement: "top",
    title: "{/literal}{'privacy_title18'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp18'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "left",
    element: "#content",
    title: "{/literal}{'privacy_title19'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{if $TAT_FTP}{'privacy_stp19'|@translate|@escape:'javascript'}{else}{'privacy_stp19_b'|@translate|@escape:'javascript'}{/if}{literal}",
    onPrev: function (tour) {window.location = "admin.php?page=photo-{/literal}{$TAT_image_id}{literal}";},

  },
  {//20
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "top",
    element: "#categoryOrdering",
    title: "{/literal}{'privacy_title20'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp20'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=cat_list",
    placement: "left",
    element: "#tabsheet:first-child",
    title: "{/literal}{'privacy_title21'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp21'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: ".selected_tab",
    title: "{/literal}{'privacy_title22'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp22'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "top",
    element: "#TAT_FC_23",
    title: "{/literal}{'privacy_title23'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp23'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}";},
    placement: "bottom",
    element: ".tabsheet",
    title: "{/literal}{'privacy_title24'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp24'|@translate|@escape:'javascript'}{literal}"
  },
  {//25
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "left",
    element: "#content",
    title: "{/literal}{'privacy_title25'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp25'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'privacy_title26'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp26'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    element: "#selectStatus",
    title: "{/literal}{'privacy_title27'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp27'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: /admin\.php\?page=album-[0-9]+-permissions/,
    redirect:function (tour) {window.location = "admin.php?page=album-{/literal}{$TAT_cat_id}{literal}-permissions";},
    placement: "top",
    title: "{/literal}{'privacy_title28'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp28'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title29'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp29'|@translate|@escape:'javascript'}{literal}"
  },
  {//30
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#gallery_title",
    title: "{/literal}{'privacy_title30'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp30'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "right",
    element: "#page_banner",
    title: "{/literal}{'privacy_title31'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp31'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    reflex: true,
    placement: "top",
    element: ".formButtons input",
    title: "{/literal}{'privacy_title32'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp32'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=configuration",
    placement: "top",
    title: "{/literal}{'privacy_stp33'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp33'|@translate|@escape:'javascript'}{literal}",
    prev:30
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title34'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp34'|@translate|@escape:'javascript'}{literal}"
  },
  {//35
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "#TAT_FC_35",
    title: "{/literal}{'privacy_title35'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp35'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title36'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp36'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=themes",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'privacy_title37'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp37'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "",
    title: "{/literal}{'privacy_title38'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp38'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "left",
    element: "#content",
    title: "{/literal}{'privacy_title39'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp39'|@translate|@escape:'javascript'}{literal}"
  },
  {//40
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "bottom",
    element: "#TakeATour",
    title: "{/literal}{'privacy_title40'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp40'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugins",
    placement: "right",
    element: ".tabsheet",
    title: "{/literal}{'privacy_title41'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp41'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=languages",
    title: "{/literal}{'privacy_title42'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp42'|@translate|@escape:'javascript'}{literal}"
  },
  {
    path: "{/literal}{$TAT_path}{literal}admin.php?page=plugin-TakeATour",
    placement: "top",
    element: "",
    title: "{/literal}{'privacy_title43'|@translate|@escape:'javascript'}{literal}",
    content: "{/literal}{'privacy_stp43'|@translate|@escape:'javascript'}{literal}"
  }
]);

// Initialize the tour
tour.init();

// Start the tour
tour.start();

jQuery( "input[class='submit']" ).click(function() {
  if (tour.getCurrentStep()==5)
  {
    tour.goTo(6);
  }
});
{/literal}{/footer_script}