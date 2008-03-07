<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

class plugins
{
  var $page = '';
  var $order = '';
  var $my_base_url = '';
  var $html_base_url = '';
  var $fs_plugins = array();
  var $db_plugins_by_id = array();
  var $server_plugins = array();

  function plugins($page='', $order='')
  {
    $this->page = $page;
    $this->order = $order;

    $this->my_base_url = get_root_url().'admin.php?page='.$this->page;
    if (!empty($this->order))
    {
      $this->my_base_url .= '&order=' . $this->order;
    }
    $this->html_base_url = htmlentities($this->my_base_url);

    $this->get_fs_plugins();
    foreach (get_db_plugins() as $db_plugin)
    {
      $this->db_plugins_by_id[$db_plugin['id']] = $db_plugin;
    }
  }

  /**
   * Assign tabsheet
   */
  function tabsheet()
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
    $link = PHPWG_ROOT_PATH.'admin.php?page=';

    $tabsheet = new tabsheet();
    $tabsheet->add('plugins_list', l10n('plugins_tab_list'), $link.'plugins_list');
    $tabsheet->add('plugins_update', l10n('plugins_tab_update'), $link.'plugins_update');
    $tabsheet->add('plugins_new', l10n('plugins_tab_new'), $link.'plugins_new');
    $tabsheet->select($this->page);
    $tabsheet->assign();
  }

  /**
   * Set order
  *  @param array - order options
   */
  function set_order_options($options)
  {
    global $template;

    $link = get_root_url().'admin.php?page='.$this->page.'&amp;order=';
    foreach($options as $key => $value)
    {
      $tpl_options[$link . $key] = $value;
    }
    $template->assign('order_options', $tpl_options);
    $template->assign('order_selected', $link . $this->order);
  }
  
 /**
   * Perform requested actions
  *  @param string - action
  * @param string - plugin id
  * @param string - errors
  */
  function perform_action($action, $plugin_id, $errors=array())
  {
    if (isset($this->db_plugins_by_id[$plugin_id]))
    {
      $crt_db_plugin = $this->db_plugins_by_id[$plugin_id];
    }
    $file_to_include = PHPWG_PLUGINS_PATH . $plugin_id . '/maintain.inc.php';

    switch ($action)
    {
      case 'install':
        if (!empty($crt_db_plugin))
        {
          array_push($errors, 'CANNOT INSTALL - ALREADY INSTALLED');
          break;
        }
        if (!isset($this->fs_plugins[$plugin_id]))
        {
          array_push($errors, 'CANNOT INSTALL - NO SUCH PLUGIN');
          break;
        }
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_install'))
          {
            plugin_install($plugin_id, $this->fs_plugins[$plugin_id]['version'], $errors);
          }
        }
        if (empty($errors))
        {
          $query = '
INSERT INTO ' . PLUGINS_TABLE . ' (id,version) VALUES ("'
. $plugin_id . '","' . $this->fs_plugins[$plugin_id]['version'] . '"
)';
          pwg_query($query);
        }
        break;

      case 'activate':
        if (!isset($crt_db_plugin))
        {
          array_push($errors, 'CANNOT ACTIVATE - NOT INSTALLED');
          break;
        }
        if ($crt_db_plugin['state'] != 'inactive')
        {
          array_push($errors, 'invalid current state ' . $crt_db_plugin['state']);
          break;
        }
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_activate'))
          {
            plugin_activate($plugin_id, $crt_db_plugin['version'], $errors);
          }
        }
        if (empty($errors))
        {
          $query = '
UPDATE ' . PLUGINS_TABLE . ' SET state="active" WHERE id="' . $plugin_id . '"';
          pwg_query($query);
        }
        break;

      case 'deactivate':
        if (!isset($crt_db_plugin))
        {
          die ('CANNOT DEACTIVATE - NOT INSTALLED');
        }
        if ($crt_db_plugin['state'] != 'active')
        {
          die('invalid current state ' . $crt_db_plugin['state']);
        }
        $query = '
UPDATE ' . PLUGINS_TABLE . ' SET state="inactive" WHERE id="' . $plugin_id . '"';
        pwg_query($query);
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_deactivate'))
          {
            plugin_deactivate($plugin_id);
          }
        }
        break;

      case 'uninstall':
        if (!isset($crt_db_plugin))
        {
          die ('CANNOT UNINSTALL - NOT INSTALLED');
        }
        $query = '
DELETE FROM ' . PLUGINS_TABLE . ' WHERE id="' . $plugin_id . '"';
        pwg_query($query);
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_uninstall'))
          {
            plugin_uninstall($plugin_id);
          }
        }
        break;

      case 'delete':
        if (!empty($crt_db_plugin))
        {
          array_push($errors, 'CANNOT DELETE - PLUGIN IS INSTALLED');
          break;
        }
        if (!isset($this->fs_plugins[$plugin_id]))
        {
          array_push($errors, 'CANNOT DELETE - NO SUCH PLUGIN');
          break;
        }
        if (!$this->deltree(PHPWG_PLUGINS_PATH . $plugin_id))
        {
          $this->send_to_trash(PHPWG_PLUGINS_PATH . $plugin_id);
        }
        break;
    }
    return $errors;
  }

  /**
  *  Returns an array of plugins defined in the plugin directory
  */  
  function get_fs_plugins()
  {
    $dir = opendir(PHPWG_PLUGINS_PATH);
    while ($file = readdir($dir))
    {
      if ($file!='.' and $file!='..')
      {
        $path = PHPWG_PLUGINS_PATH.$file;
        if (is_dir($path) and !is_link($path)
            and preg_match('/^[a-zA-Z0-9-_]+$/', $file )
            and file_exists($path.'/main.inc.php')
            )
        {
          $plugin = array(
              'name'=>$file,
              'version'=>'0',
              'uri'=>'',
              'description'=>'',
              'author'=>'',
            );
          $plg_data = implode( '', file($path.'/main.inc.php') );

          if ( preg_match("|Plugin Name: (.*)|", $plg_data, $val) )
          {
            $plugin['name'] = trim( $val[1] );
          }
          if (preg_match("|Version: (.*)|", $plg_data, $val))
          {
            $plugin['version'] = trim($val[1]);
          }
          if ( preg_match("|Plugin URI: (.*)|", $plg_data, $val) )
          {
            $plugin['uri'] = trim($val[1]);
          }
          if ( preg_match("|Description: (.*)|", $plg_data, $val) )
          {
            $plugin['description'] = trim($val[1]);
          }
          if ( preg_match("|Author: (.*)|", $plg_data, $val) )
          {
            $plugin['author'] = trim($val[1]);
          }
          if ( preg_match("|Author URI: (.*)|", $plg_data, $val) )
          {
            $plugin['author uri'] = trim($val[1]);
          }
          if (!empty($plugin['uri']) and strpos($plugin['uri'] , 'extension_view.php?eid='))
          {
            list( , $extension) = explode('extension_view.php?eid=', $plugin['uri']);
            if (is_numeric($extension)) $plugin['extension'] = $extension;
          }
          // IMPORTANT SECURITY !
          $plugin = array_map('htmlspecialchars', $plugin);
          $this->fs_plugins[$file] = $plugin;
        }
      }
    }
    closedir($dir);
  }

  /**
   * sort fs_plugins
   */
  function sort_fs_plugins()
  {
    switch ($this->order)
    {
      case 'status':
        $this->sort_plugins_by_state();
        break;
      case 'author':
        uasort($this->fs_plugins, array($this, 'plugin_author_compare'));
        break;
      case 'id':
        uksort($this->fs_plugins, 'strcasecmp');
        break;
      default: //sort by plugin name
        uasort($this->fs_plugins, 'name_compare');
    }
  }

  /**
   * Retrieve PEM server datas
   */
  function check_server_plugins()
  {
    foreach($this->fs_plugins as $plugin_id => $fs_plugin)
    {
      if (isset($fs_plugin['extension']))
      {
        $plugins_to_check[] = $fs_plugin['extension'];
      }
    }
    $url = PEM_URL . '/uptodate.php?version=' . rawurlencode(PHPWG_VERSION) . '&extensions=' . implode(',', $plugins_to_check);
    $url .= $this->page == 'plugins_new' ? '&newext=Plugin' : '';

    if (!empty($plugins_to_check) and $source = @file_get_contents($url))
    {
      $this->server_plugins = @unserialize($source);
      switch ($this->order)
      {
        case 'name':
          uasort($this->server_plugins, array($this, 'extension_name_compare'));
          break;
        case 'author':
          uasort($this->server_plugins, array($this, 'extension_author_compare'));
          break;
        default: // sort by id desc
          krsort($this->server_plugins);
      }
    }
    else
    {
      $this->server_plugins = false;
    }
  }

 /**
   * Upgrade plugin
  *  @param string - archive URL
  * @param string - plugin id
  */
  function upgrade($source, $plugin_id)
  {
    if (isset($this->db_plugins_by_id[$plugin_id])
      and $this->db_plugins_by_id[$plugin_id]['state'] == 'active')
    {
      $this->perform_action('deactivate', $plugin_id);

      redirect(
        $this->my_base_url
        . '&upgrade=' . $source
        . '&plugin=' . $plugin_id
        . '&reactivate=true');
    }

    include(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
    $upgrade_status = $this->extract_plugin_files('upgrade', $source, $plugin_id);

    if (isset($_GET['reactivate']))
    {
      $this->perform_action('activate', $plugin_id);
    }
    redirect($this->my_base_url.'&plugin='.$plugin_id.'&upgradestatus='.$upgrade_status);
  }


  /**
   * Install plugin
  *  @param string - archive URL
  * @param string - extension id
  */
  function install($source, $extension)
  {
    include(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
    $install_status = $this->extract_plugin_files('install', $source, $extension);

    redirect($this->my_base_url.'&installstatus='.$install_status);
  }


  /**
   * Extract plugin files from archive
   * @param string - install or upgrade
   *  @param string - archive URL
    * @param string - destination path
   */
  function extract_plugin_files($action, $source, $dest)
  {
    if ($archive = tempnam( PHPWG_PLUGINS_PATH, 'zip'))
    {
      if (@copy(PEM_URL . str_replace(' ', '%20', $source), $archive))
      {
        $zip = new PclZip($archive);
        if ($list = $zip->listContent())
        {
          foreach ($list as $file)
          {
            // we search main.inc.php in archive
            if (basename($file['filename']) == 'main.inc.php'
              and (!isset($main_filepath)
              or strlen($file['filename']) < strlen($main_filepath)))
            {
              $main_filepath = $file['filename'];
            }
          }
          if (isset($main_filepath))
          {
            $root = dirname($main_filepath); // main.inc.php path in archive
            if ($action == 'upgrade')
            {
              $extract_path = PHPWG_PLUGINS_PATH.$dest;
            }
            else
            {
              $extract_path = PHPWG_PLUGINS_PATH
                  . ($root == '.' ? 'extension_' . $dest : basename($root));
            }
            if($result = $zip->extract(PCLZIP_OPT_PATH, $extract_path,
                                       PCLZIP_OPT_REMOVE_PATH, $root,
                                       PCLZIP_OPT_REPLACE_NEWER))
            {
              foreach ($result as $file)
              {
                if ($file['stored_filename'] == $main_filepath)
                {
                  $status = $file['status'];
                  break;
                }
              }
            }
            else $status = 'extract_error';
          }
          else $status = 'archive_error';
        }
        else $status = 'archive_error';
      }
      else $status = 'dl_archive_error';
    }
    else $status = 'temp_path_error';

    @unlink($archive);
    return $status;
  }
  
  /**
   * get install or upgrade result
   */
  function get_result($result, $plugin='')
  {
    global $page;

    switch ($result)
    {
      case 'ok':
        if ($this->page == 'plugins_update')
        {
          array_push($page['infos'],
             sprintf(
                l10n('plugins_upgrade_ok'),
                $this->fs_plugins[$plugin]['name']));
        }
        else
        {
          array_push($page['infos'],
            l10n('plugins_install_ok'),
            l10n('plugins_install_need_activate'));
        }
        break;

      case 'temp_path_error':
        array_push($page['errors'], l10n('plugins_temp_path_error'));
        break;

      case 'dl_archive_error':
        array_push($page['errors'], l10n('plugins_dl_archive_error'));
        break;

      case 'archive_error':
        array_push($page['errors'], l10n('plugins_archive_error'));
        break;

      default:
        array_push($page['errors'],
          sprintf(l10n('plugins_extract_error'), $result),
          l10n('plugins_check_chmod'));
    }  
  }

  /**
   * delete $path directory
   * @param string - path
   */
  function deltree($path)
  {
    if (is_dir($path))
    {
      $fh = opendir($path);
      while ($file = readdir($fh))
      {
        if ($file != '.' and $file != '..')
        {
          $pathfile = $path . '/' . $file;
          if (is_dir($pathfile))
          {
            $this->deltree($pathfile);
          }
          else
          {
            @unlink($pathfile);
          }
        }
      }
      closedir($fh);
      return @rmdir($path);
    }
  }

  /**
   * send $path to trash directory
    * @param string - path
   */
  function send_to_trash($path)
  {
    $trash_path = PHPWG_PLUGINS_PATH . 'trash';
    if (!is_dir($trash_path))
    {
      @mkdir($trash_path);
      $file = @fopen($trash_path . '/.htaccess', 'w');
      @fwrite($file, 'deny from all');
      @fclose($file);
    }
    while ($r = $trash_path . '/' . md5(uniqid(rand(), true)))
    {
      if (!is_dir($r))
      {
        @rename($path, $r);
        break;
      }
    }
  }

  /**
   * Sort functions
   */
  function plugin_version_compare($a, $b)
  {
    $r = version_compare($a['version'], $b['version']);
    if ($r == 0) return strcasecmp($a['version'], $b['version']);
    else return $r;
  }

  function extension_name_compare($a, $b)
  {
    return strcmp(strtolower($a['ext_name']), strtolower($b['ext_name']));
  }

  function extension_author_compare($a, $b)
  {
    $r = strcasecmp($a['author'], $b['author']);
    if ($r == 0) return $this->extension_name_compare($a, $b);
    else return $r;
  }

  function plugin_author_compare($a, $b)
  {
    $r = strcasecmp($a['author'], $b['author']);
    if ($r == 0) return name_compare($a, $b);
    else return $r;
  }

  function sort_plugins_by_state()
  {
    uasort($this->fs_plugins, 'name_compare');

    $active_plugins = array();
    $inactive_plugins = array();
    $not_installed = array();

    foreach($this->fs_plugins as $plugin_id => $plugin)
    {
      if (isset($this->db_plugins_by_id[$plugin_id]))
      {
        $this->db_plugins_by_id[$plugin_id]['state'] == 'active' ?
          $active_plugins[$plugin_id] = $plugin : $inactive_plugins[$plugin_id] = $plugin;
      }
      else
      {
        $not_installed[$plugin_id] = $plugin;
      }
    }
    $this->fs_plugins = $active_plugins + $inactive_plugins + $not_installed;
  }
}
?>