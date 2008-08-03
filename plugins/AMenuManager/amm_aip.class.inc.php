<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AIP classe => manage integration in administration interface

  --------------------------------------------------------------------------- */
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'AMenuManager/amm_root.class.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/css.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/ajax.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/genericjs.class.inc.php');

class AMM_AIP extends AMM_root
{ 
  protected $tabsheet;
  protected $css;   //the css object
  protected $ajax;

  protected $urls_modes=array(0 => 'new_window', 1 => 'current_window');

  function AMM_AIP($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);

    $this->load_config();
    $this->init_events();

    $this->tabsheet = new tabsheet();
    $this->tabsheet->add('setmenu',
                          l10n('g002_setmenu'),
                          $this->page_link.'&amp;fAMM_tabsheet=setmenu');
    $this->tabsheet->add('links',
                          l10n('g002_addlinks'),
                          $this->page_link.'&amp;fAMM_tabsheet=links');
    $this->tabsheet->add('randompict',
                          l10n('g002_randompict'),
                          $this->page_link.'&amp;fAMM_tabsheet=randompict');
    $this->tabsheet->add('personnalblock',
                          l10n('g002_personnalblock'),
                          $this->page_link.'&amp;fAMM_tabsheet=personnalblock');
    $this->css = new css(dirname($this->filelocation).'/'.$this->plugin_name_files.".css");
    $this->ajax = new Ajax();
  }


  /* ---------------------------------------------------------------------------
  Public classe functions
  --------------------------------------------------------------------------- */

  /*
    manage plugin integration into piwigo's admin interface
  */
  public function manage()
  {
    global $template;


    $template->set_filename('plugin_admin_content', dirname(__FILE__)."/admin/amm_admin.tpl");

    $this->return_ajax_content();

    $this->init_request();

    $this->tabsheet->select($_REQUEST['fAMM_tabsheet']);
    $this->tabsheet->assign();
    $selected_tab=$this->tabsheet->get_selected();
    $template->assign($this->tabsheet->get_titlename(), "[".$selected_tab['caption']."]");

    $template_plugin["AMM_VERSION"] = "<i>".$this->plugin_name."</i> ".l10n('g002_version').AMM_VERSION;
    $template_plugin["AMM_PAGE"] = $_REQUEST['fAMM_tabsheet'];
    $template_plugin["PATH"] = AMM_PATH;

    $template->assign('plugin', $template_plugin);


    if(isset($_POST['famm_modeedit']))
    {
      $post_action=$_POST['famm_modeedit'];
    }
    else
    {
      $post_action="";
    }

    $page_nfo="";
    if($_REQUEST['fAMM_tabsheet']=='links')
    {
      $page_nfo=l10n('g002_addlinks_nfo');

      switch($_REQUEST['action'])
      {
        case 'list':
          $this->display_links_list_page();
          break;
        case 'create':
        case 'modify':
          if($post_action==$_REQUEST['action'])
          {
            if(!$this->adviser_abort())
            {
              $this->action_create_modify_url();
            }
            $this->display_links_list_page();
          }
          else
          {
            $this->display_links_manage_page($_REQUEST['action']);
          }
          break;
        case 'config':
          if($post_action==$_REQUEST['action'])
          {
            if(!$this->adviser_abort())
            {
              $this->action_modify_config();
            }
          }
          $this->display_links_config_page();
          break;
      }
    }
    elseif($_REQUEST['fAMM_tabsheet']=='randompict')
    {
      $page_nfo=l10n('g002_randompict_nfo');
    }
    elseif($_REQUEST['fAMM_tabsheet']=='personnalblock')
    {
      $page_nfo=l10n('g002_personnalblock_nfo');
    }
    elseif($_REQUEST['fAMM_tabsheet']=='setmenu')
    {
      $page_nfo=l10n('g002_setmenu_nfo');
      $this->display_sections_list_page();
    }

    $template->assign('page_nfo', $page_nfo);

    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
  }

  /*
    initialize events call for the plugin
  */
  public function init_events()
  {
    add_event_handler('menubar_file', array(&$this, 'plugin_public_menu') );
    add_event_handler('loc_end_page_header', array(&$this->css, 'apply_CSS'));
  }

  /* ---------------------------------------------------------------------------
  Private classe functions
  --------------------------------------------------------------------------- */

  /*
    return ajax content
  */
  protected function return_ajax_content()
  {
    global $ajax, $template;

    if(isset($_REQUEST['ajaxfct']))
    {
      //$this->debug("AJAXFCT:".$_REQUEST['ajaxfct']);
      $result="<p class='errors'>".l10n('g002_error_invalid_ajax_call')."</p>";
      switch($_REQUEST['ajaxfct'])
      {
        case 'links_list':
          $result=$this->ajax_amm_links_list();
          break;
        case 'links_permut':
          $result=$this->ajax_amm_links_permut($_REQUEST['fItem'], $_REQUEST['fPermut']);
          break;
        case 'links_delete':
          $result=$this->ajax_amm_links_delete($_REQUEST['fItem']);
          break;
        case 'sections_list':
          $result=$this->ajax_amm_section_list();
          break;
        case 'sections_position':
          $result=$this->ajax_amm_section_position($_REQUEST['fItem'], $_REQUEST['fPosition']);
          break;
        case 'sections_showhide':
          $result=$this->ajax_amm_section_showhide($_REQUEST['fItem']);
          break;
      }
      //$template->
      $this->ajax->return_result($result);
    }
  }

  /*
    if empty, initialize $_request 
  */
  private function init_request()
  {
    //initialise $REQUEST values if not defined
    if(!array_key_exists('fAMM_tabsheet', $_REQUEST))
    {
      $_REQUEST['fAMM_tabsheet']='setmenu';
    }

    if(($_REQUEST['fAMM_tabsheet']=='links') and !isset($_REQUEST['action']))
    {
      $_REQUEST['action']='list';
    }
    

  } //init_request


  /*
    manage display for urls table page
  */
  private function display_links_list_page()
  {
    global $template, $user;
    $template->set_filename('body_page',
                            dirname($this->filelocation).'/admin/amm_linkslist.tpl');

    $tmp=$this->get_count_url();
    if($tmp==0)
    {
      $tmp=l10n("g002_nolinks");
    }
    elseif($tmp==1)
    {
      $tmp="1 ".l10n("g002_link");
    }
    else
    {
      $tmp=$tmp." ".l10n("g002_links");
    }


    $template_datas=array(
      'lnk_create' => $this->page_link.'&amp;fAMM_tabsheet=links&amp;action=create',
      'lnk_config' => $this->page_link.'&amp;fAMM_tabsheet=links&amp;action=config',
      'AMM_AJAX_URL_LIST' => $this->page_link."&ajaxfct=",
      'nburl' => $tmp
    );
    
    $template->assign("datas", $template_datas);
    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
  }

  /*
    manage display for urls config page
  */
  private function display_links_config_page()
  {
    global $template, $user;
    $template->set_filename('body_page',
                            dirname($this->filelocation).'/admin/amm_linksconfig.tpl');

    $template_datas=array(
      'lnk_list' => $this->page_link.'&amp;fAMM_tabsheet=links',
      'AMM_AJAX_URL_LIST' => $this->page_link."&ajaxfct=",
      'show_icons_selected' => $this->my_config['amm_links_show_icons'],
      'active_selected' => $this->my_config['amm_links_active'],
      'lang_selected' => $user['language']
    );

    $template_datas['language_list'] = array();
    foreach($this->my_config['amm_links_title'] as $key => $val)
    {
      $template_datas['language_list'][] = array(
        'LANG' => $key,
        'MENUBARTIT' => base64_decode($val)
      );
    }



    $lang=get_languages();
    foreach($lang as $key => $val)
    {
      $template_datas['language_list_values'][] = $key;
      $template_datas['language_list_labels'][] = $val;
    }


    $template_datas['yesno_values'] = array('y','n');
    $template_datas['yesno_labels'][] = l10n('g002_yesno_y');
    $template_datas['yesno_labels'][] = l10n('g002_yesno_n');

    
    $template->assign("datas", $template_datas);
    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
  }

  /*
    manage display for urls create/modify page
  */
  private function display_links_manage_page($modeedit = 'create', $urlid=0)
  {
    global $template, $user;
    $template->set_filename('body_page',
                            dirname($this->filelocation).'/admin/amm_linkslist_edit.tpl');

    $extensions_list=array('jpg'=>0,'jpeg'=>0,'gif'=>0,'png'=>0);
    $template_icons_list=array();
    $directory=dir(dirname($this->filelocation).'/links_pictures/');
    while($file=$directory->read())
    {
      if(isset($extensions_list[get_extension(strtolower($file))]))
      {
        $template_icons_list[]=$file;
      }
    }
    

    if($modeedit=='modify')
    {
      $url=$this->get_url($urlid);

      $template_datas=array(
        'id' => $urlid,
        'modeedit' => 'modify',
        'label' => $url['label'],
        'url' => $url['url'],
        'icons_selected' => $url['icon'],
        'mode_selected' => $url['mode'],
        'visible_selected' => $url['visible']
      );
    }
    else
    {
      $template_datas=array(
        'id' => '',
        'modeedit' => 'create',
        'label' => '',
        'url' => '',
        'icons_selected' => $template_icons_list[0],
        'mode_selected' => 0,
        'visible_selected' => 'y'
      );
    }

    $template_datas['lnk_list'] = $this->page_link.'&amp;fAMM_tabsheet=links';
    $template_datas['icons_img'] = AMM_PATH."links_pictures/".$template_datas['icons_selected'];
    $template_datas['icons_values'] = array();
    foreach($template_icons_list as $key => $val)
    {
      $template_datas['icons_values'][] = array(
        'img' => AMM_PATH."links_pictures/".$val,
        'value' => $val,
        'label' => $val
      );
    }
    $template_datas['mode_values'] = array(0,1);
    $template_datas['mode_labels'][] = l10n("g002_mode_".$this->urls_modes[0]);
    $template_datas['mode_labels'][] = l10n("g002_mode_".$this->urls_modes[1]);
    $template_datas['visible_values'] = array('y','n');
    $template_datas['visible_labels'][] = l10n('g002_yesno_y');
    $template_datas['visible_labels'][] = l10n('g002_yesno_n');

    $template->assign("datas", $template_datas);
    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
  }

  /*
    manage create/modify url into database and display result
  */
  protected function action_create_modify_url()
  {
    $datas=array(
      'id' => $_POST['famm_id'],
      'label' => $_POST['famm_label'],
      'url' => $_POST['famm_url'],
      'mode' => $_POST['famm_mode'],
      'icon' => $_POST['famm_icon'],
      'position' => 0,
      'visible' => $_POST['famm_visible']
    );

    switch($_POST['famm_modeedit'])
    {
      case 'create':
        $this->add_url($datas);
        break;
      case 'modify':
        $this->modify_url($datas);
    }
  }

  /*
    manage urls config save into database 
  */
  protected function action_modify_config()
  {
    $this->my_config['amm_links_show_icons']=$_POST['famm_links_show_icons'];
    $this->my_config['amm_links_active']=$_POST['famm_links_active'];
    $languages=get_languages();
    foreach($languages as $key => $val)
    {
      $this->my_config['amm_links_title'][$key]=base64_encode(stripslashes($_POST['famm_links_title_'.$key]));
    }
    $this->save_config();
  }

  /*
    manage display for sections table page
  */
  private function display_sections_list_page()
  {
    global $template, $user;
    $template->set_filename('body_page',
                            dirname($this->filelocation).'/admin/amm_sectionslist.tpl');

    $template_datas=array(
      'AMM_AJAX_URL_LIST' => $this->page_link."&ajaxfct=",
    );
    
    $template->assign("datas", $template_datas);
    $template->assign_var_from_handle('AMM_BODY_PAGE', 'body_page');
  }





  /*
    manage adviser profile
      return true if user is adviser
  */
  protected function adviser_abort()
  {
    if(is_adviser())
    {
      $this->display_result(l10n("g002_adviser_not_allowed"), false);
      return(true);
    }
    return(false);
  }

  /* ---------------------------------------------------------------------------
    functions to manage urls tables
  --------------------------------------------------------------------------- */
  // protected function get_urls()
  // protected function get_count_url()
  // => defined in root class

  // return properties of an given url
  private function get_url($url_id)
  {
    $returned=array();
    $sql="SELECT * FROM ".$this->tables['urls']." WHERE id = '".$url_id."'";
    $result=pwg_query($sql);
    if($result)
    {
      $returned=mysql_fetch_array($result);
    }
    return($returned);
  }

  // permut position of two 2 urls
  private function permut_url($url_id, $url_permut)
  {
    $sql="SELECT id, position FROM ".$this->tables['urls']." WHERE id IN ('".$url_id."','".$url_permut."')";
    $result=pwg_query($sql);
    if($result)
    {
      $tmp=array();
      while($row=mysql_fetch_array($result))
      {
        $tmp[$row['id']]=$row['position'];
      }
      $sql="UPDATE ".$this->tables['urls']." SET position = ".$tmp[$url_id]." WHERE id = '".$url_permut."'";
      pwg_query($sql);
      $sql="UPDATE ".$this->tables['urls']." SET position = ".$tmp[$url_permut]." WHERE id = '".$url_id."'";
      pwg_query($sql);
    }
  }

  // delete an url
  private function delete_url($url_id)
  {
    $sql="DELETE FROM ".$this->tables['urls']." WHERE id = '".$url_id."' ";
    return(pwg_query($sql));
  }

  // add an url
  private function add_url($datas)
  {
    $numurl=$this->get_count_url();
    $sql="INSERT INTO ".$this->tables['urls']." (id, label, url, mode, icon, position, visible)
          VALUES ('', '".addslashes($datas['label'])."', '".$datas['url']."', '".$datas['mode']."',
                  '".$datas['icon']."', '".$numurl."', '".$datas['visible']."')";
    return(pwg_query($sql));
  }

  // modify an url
  private function modify_url($datas)
  {
    $sql="UPDATE ".$this->tables['urls']." SET label = '".addslashes($datas['label'])."',
          url = '".$datas['url']."', mode = '".$datas['mode']."', icon = '".$datas['icon']."',
          visible = '".$datas['visible']."'
          WHERE id = '".$datas['id']."'";
    return(pwg_query($sql));
  }

  // just modify url visibility 
  private function set_url_visibility($urlid, $visible)
  {
    $sql="UPDATE ".$this->tables['urls']." SET visible = '".$visible."'
          WHERE id = '".$urlid."'";
    return(pwg_query($sql));
  }


  /* ---------------------------------------------------------------------------
    ajax functions
  --------------------------------------------------------------------------- */

  // return a html formatted list of urls
  private function ajax_amm_links_list()
  {
    global $template, $user;
    $local_tpl = new Template(AMM_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->filelocation).'/admin/amm_linkslist_detail.tpl');

    $template_datas['urls']=array();
    $urls=$this->get_urls();
    for($i=0;$i<count($urls);$i++)
    {
      $template_datas['urls'][]=array(
        'img' => AMM_PATH."links_pictures/".$urls[$i]['icon'],
        'label' => $urls[$i]['label'],
        'url' => $urls[$i]['url'],
        'mode' => l10n("g002_mode_".$this->urls_modes[$urls[$i]['mode']]),
        'up' =>  ($i==0)?false:true,
        'down' =>  ($i<(count($urls)-1))?true:false,
        'edit' => $this->page_link.'&amp;fAMM_tabsheet=links&amp;action=modify&amp;fItem='.$urls[$i]['id'],
        'ID' => $urls[$i]['id'],
        'IDPREV' => ($i==0)?0:$urls[$i-1]['id'],
        'IDNEXT' => ($i<(count($urls)-1))?$urls[$i+1]['id']:0,
        'visible' => l10n('g002_yesno_'.$urls[$i]['visible'])
      );
    }

    $themeconf=array(
      'icon_dir' => $template->get_themeconf('icon_dir')
    );

    $local_tpl->assign('themeconf', $themeconf);
    $local_tpl->assign('datas', $template_datas);
    $local_tpl->assign('plugin', array('PATH' => AMM_PATH));

    return($local_tpl->parse('body_page', true));
  }

  // permut position of 2 urls and returns a html formatted list of urls
  private function ajax_amm_links_permut($urlid, $urlpermut)
  {
    $this->permut_url($urlid, $urlpermut);
    return($this->ajax_amm_links_list());
  }

  // delete an url and returns a html formatted list of urls
  private function ajax_amm_links_delete($urlid)
  {
    if(!$this->adviser_abort())
    {
      $this->delete_url($urlid);
    }
    return($this->ajax_amm_links_list());
  }




  // return a html formatted list of menu's sections
  private function ajax_amm_section_list()
  {
    global $menu;
    $local_tpl = new Template(AMM_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->filelocation).'/admin/amm_sectionslist_detail.tpl');

    $sections=$menu->registered();
    $i=0;
    foreach($sections as $key => $val)
    {     
      $template_datas['sections'][]=array(
        'OWNER' => $val['OWNER'],
        'NAME' => l10n($val['NAME']),
        'ID' => $key,
        'VISIBLE' => l10n('g002_yesno_'.$this->my_config['amm_sections_visible'][$key]),
        'POSITION' => $val['POSITION'],
        'NEXTPOS' => $val['POSITION']+2,
        'PREVPOS' => $val['POSITION']-1,
        'up' =>  ($i==0)?false:true,
        'down' =>  ($i<(count($sections)-1))?true:false
      );
      $i++;
    }

    $local_tpl->assign('datas', $template_datas);
    $local_tpl->assign('plugin', array('PATH' => AMM_PATH));

    return($local_tpl->parse('body_page', true));
  }

  // move item to the specified position
  private function ajax_amm_section_position($urlid, $position)
  {
    global $menu;

    $menu->register_position($urlid, $position);
    return($this->ajax_amm_section_list());
  }

  // move item to the specified position
  private function ajax_amm_section_showhide($urlid)
  {
    global $menu;

    $switchvisible=array('y'=>'n', 'n'=>'y');

    $this->my_config['amm_sections_visible'][$urlid]=$switchvisible[$this->my_config['amm_sections_visible'][$urlid]];
    $this->save_config();

    return($this->ajax_amm_section_list());
  }


} // AMM_AIP class


?>
