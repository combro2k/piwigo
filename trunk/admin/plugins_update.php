<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

$template->set_filenames(array('plugins' => 'plugins_update.tpl'));

$base_url = get_root_url().'admin.php?page='.$page['page'];

$plugins = new plugins();

//-----------------------------------------------------------automatic upgrade
if (isset($_GET['plugin']) and isset($_GET['revision']))
{
  if (!is_webmaster())
  {
    array_push($page['errors'], l10n('Webmaster status is required.'));
  }
  else
  {
    check_pwg_token();
    
    $plugin_id = $_GET['plugin'];
    $revision = $_GET['revision'];

    if (isset($plugins->db_plugins_by_id[$plugin_id])
      and $plugins->db_plugins_by_id[$plugin_id]['state'] == 'active')
    {
      $plugins->perform_action('deactivate', $plugin_id);

      redirect($base_url
        . '&revision=' . $revision
        . '&plugin=' . $plugin_id
        . '&pwg_token='.get_pwg_token()
        . '&reactivate=true');
    }

    $upgrade_status = $plugins->extract_plugin_files('upgrade', $revision, $plugin_id);

    if (isset($_GET['reactivate']))
    {
      $plugins->perform_action('activate', $plugin_id);
    }

    $template->delete_compiled_templates();

    redirect($base_url.'&plugin='.$plugin_id.'&upgradestatus='.$upgrade_status);
  }
}

//--------------------------------------------------------------upgrade result
if (isset($_GET['upgradestatus']) and isset($_GET['plugin']))
{
  switch ($_GET['upgradestatus'])
  {
    case 'ok':
      array_push($page['infos'],
         sprintf(
            l10n('%s has been successfully upgraded.'),
            $plugins->fs_plugins[$_GET['plugin']]['name']));
      break;

    case 'temp_path_error':
      array_push($page['errors'], l10n('Can\'t create temporary file.'));
      break;

    case 'dl_archive_error':
      array_push($page['errors'], l10n('Can\'t download archive.'));
      break;

    case 'archive_error':
      array_push($page['errors'], l10n('Can\'t read or extract archive.'));
      break;

    default:
      array_push($page['errors'],
        sprintf(l10n('An error occured during extraction (%s).'), $_GET['upgradestatus']),
        l10n('Please check "plugins" folder and sub-folders permissions (CHMOD).'));
  }  
}

//--------------------------------------------------------------------Tabsheet
$plugins->set_tabsheet($page['page']);

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
if ($plugins->get_server_plugins())
{
  foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (isset($fs_plugin['extension'])
      and isset($plugins->server_plugins[$fs_plugin['extension']]))
    {
      $plugin_info = $plugins->server_plugins[$fs_plugin['extension']];

      if (!$plugins->plugin_version_compare($fs_plugin['version'], $plugin_info['revision_name']))
      {
        $url_auto_update = $base_url
          . '&amp;revision=' . $plugin_info['revision_id']
          . '&amp;plugin=' . $plugin_id
          . '&amp;pwg_token='.get_pwg_token()
          ;

        $template->append('plugins', array(
          'ID' => $plugin_info['extension_id'],
          'EXT_NAME' => $fs_plugin['name'],
          'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$plugin_info['extension_id'],
          'EXT_DESC' => trim($plugin_info['extension_description'], " \n\r"),
          'REV_DESC' => trim($plugin_info['revision_description'], " \n\r"),
          'CURRENT_VERSION' => $fs_plugin['version'],
          'NEW_VERSION' => $plugin_info['revision_name'],
          'AUTHOR' => $plugin_info['author_name'],
          'DOWNLOADS' => $plugin_info['extension_nb_downloads'],
          'URL_UPDATE' => $url_auto_update,
          'URL_DOWNLOAD' => $plugin_info['download_url'] . '&amp;origin=piwigo_download'));
      }
    }
  }
}
else
{
  $template->assign('SERVER_ERROR', true);
  array_push($page['errors'], l10n('Can\'t connect to server.'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>