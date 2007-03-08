<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

$my_base_url = PHPWG_ROOT_PATH.'admin.php?page=plugins';


// +-----------------------------------------------------------------------+
// |                     perform requested actions                         |
// +-----------------------------------------------------------------------+
if ( isset($_GET['action']) and isset($_GET['plugin'])  )
{
  $plugin_id = $_GET['plugin'];
  $crt_db_plugin = get_db_plugins('', $plugin_id);
  if (!empty($crt_db_plugin))
  {
    $crt_db_plugin=$crt_db_plugin[0];
  }
  else
  {
    unset($crt_db_plugin);
  }

  $errors = array();
  $file_to_include = PHPWG_PLUGINS_PATH.$plugin_id.'/maintain.inc.php';

  switch ( $_GET['action'] )
  {
    case 'install':
      if ( !empty($crt_db_plugin))
      {
        array_push($errors, 'CANNOT install - ALREADY INSTALLED');
        break;
      }
      $fs_plugins = get_fs_plugins();
      if ( !isset( $fs_plugins[$plugin_id] ) )
      {
        array_push($errors, 'CANNOT install - NO SUCH PLUGIN');
        break;
      }
      if ( file_exists($file_to_include) )
      {
        include_once($file_to_include);
        if ( function_exists('plugin_install') )
        {
          plugin_install($plugin_id, $fs_plugins[$plugin_id]['version'], $errors);
        }
      }
      if (empty($errors))
      {
        $query = '
INSERT INTO '.PLUGINS_TABLE.' (id,version) VALUES ("'
.$plugin_id.'","'.$fs_plugins[$plugin_id]['version'].'"
)';
        pwg_query($query);
      }
      break;

    case 'activate':
      if ( !isset($crt_db_plugin) )
      {
        array_push($errors, 'CANNOT '. $_GET['action'] .' - NOT INSTALLED');
        break;
      }
      if ($crt_db_plugin['state']!='inactive')
      {
        array_push($errors, 'invalid current state '.$crt_db_plugin['state']);
        break;
      }
      if ( file_exists($file_to_include) )
      {
        include_once($file_to_include);
        if ( function_exists('plugin_activate') )
        {
          plugin_activate($plugin_id, $crt_db_plugin['version'], $errors);
        }
      }
      if (empty($errors))
      {
        $query = '
UPDATE '.PLUGINS_TABLE.' SET state="active" WHERE id="'.$plugin_id.'"';
        pwg_query($query);
      }
      break;

    case 'deactivate':
      if ( !isset($crt_db_plugin) )
      {
        die ('CANNOT '. $_GET['action'] .' - NOT INSTALLED');
      }
      if ($crt_db_plugin['state']!='active')
      {
        die('invalid current state '.$crt_db_plugin['state']);
      }
      $query = '
UPDATE '.PLUGINS_TABLE.' SET state="inactive" WHERE id="'.$plugin_id.'"';
      pwg_query($query);

      @include_once($file_to_include);
      if ( function_exists('plugin_deactivate') )
      {
        plugin_deactivate($plugin_id);
      }
      break;

    case 'uninstall':
      if ( !isset($crt_db_plugin) )
      {
        die ('CANNOT '. $_GET['action'] .' - NOT INSTALLED');
      }
      $query = '
DELETE FROM '.PLUGINS_TABLE.' WHERE id="'.$plugin_id.'"';
      pwg_query($query);

      @include_once($file_to_include);
      if ( function_exists('plugin_uninstall') )
      {
        plugin_uninstall($plugin_id);
      }
      break;
  }
  if (empty($errors))
  {
    // do the redirection so that we allow the plugins to load/unload
    redirect($my_base_url);
  }
  else
  {
    $page['errors'] = array_merge($page['errors'], $errors);
  }
}


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$fs_plugins = get_fs_plugins();
$db_plugins = get_db_plugins();
$db_plugins_by_id=array();
foreach ($db_plugins as $db_plugin)
{
  $db_plugins_by_id[$db_plugin['id']] = $db_plugin;
}


$template->set_filenames(array('plugins' => 'admin/plugins.tpl'));

$num=0;
foreach( $fs_plugins as $plugin_id => $fs_plugin )
{
  $display_name = $fs_plugin['name'];
  if ( !empty($fs_plugin['uri']) )
  {
    $display_name='<a href="'.$fs_plugin['uri'].'">'.$display_name.'</a>';
  }
  $desc = $fs_plugin['description'];
  if (!empty($fs_plugin['author']))
  {
    $desc.= ' (<em>';
    if (!empty($fs_plugin['author uri']))
    {
      $desc.= '<a href="'.$fs_plugin['author uri'].'">'.$fs_plugin['author'].'</a>';
    }
    else
    {
      $desc.= $fs_plugin['author'];
    }
    $desc.= '</em>)';
  }
  $template->assign_block_vars( 'plugins.plugin',
      array(
        'NAME' => $display_name,
        'VERSION' => $fs_plugin['version'],
        'DESCRIPTION' => $desc,
        'CLASS' => ($num++ % 2 == 1) ? 'row2' : 'row1',
        )
     );


  $action_url = $my_base_url.'&amp;plugin='.$plugin_id;
  if ( isset($db_plugins_by_id[$plugin_id]) )
  { // already in the database
    // MAYBE TODO HERE: check for the version and propose upgrade action
    switch ($db_plugins_by_id[$plugin_id]['state'])
    {
      case 'active':
        $template->assign_block_vars( 'plugins.plugin.action',
            array(
              'U_ACTION' => $action_url . '&amp;action=deactivate',
              'L_ACTION' => l10n('Deactivate'),
            )
          );
        break;
      case 'inactive':
        $template->assign_block_vars( 'plugins.plugin.action',
            array(
              'U_ACTION' => $action_url . '&amp;action=activate',
              'L_ACTION' => l10n('Activate'),
            )
          );
        $template->assign_block_vars( 'plugins.plugin.action',
            array(
              'U_ACTION' => $action_url . '&amp;action=uninstall',
              'L_ACTION' => l10n('Uninstall'),
            )
          );
        $template->assign_block_vars( 'plugins.plugin.action.confirm', array());
        break;
    }
  }
  else
  {
    $template->assign_block_vars( 'plugins.plugin.action',
        array(
          'U_ACTION' => $action_url . '&amp;action=install',
          'L_ACTION' => l10n('Install'),
        )
      );
    $template->assign_block_vars( 'plugins.plugin.action.confirm', array());
  }
}

$missing_plugin_ids = array_diff(
    array_keys($db_plugins_by_id), array_keys($fs_plugins)
  );
foreach( $missing_plugin_ids as $plugin_id )
{
  $template->assign_block_vars( 'plugins.plugin',
      array(
        'NAME' => $plugin_id,
        'VERSION' => $db_plugins_by_id[$plugin_id]['version'],
        'DESCRIPTION' => "ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW !",
        'CLASS' => ($num++ % 2 == 1) ? 'row2' : 'row1',
        )
     );
   $action_url = $my_base_url.'&amp;plugin='.$plugin_id;
        $template->assign_block_vars( 'plugins.plugin.action',
            array(
              'U_ACTION' => $action_url . '&amp;action=uninstall',
              'L_ACTION' => l10n('Uninstall'),
            )
          );

}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>
