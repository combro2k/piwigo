<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

/* Returns an array of plugins defined in the plugin directory
*/
function get_fs_plugins()
{
  $plugins = array();

  $dir = opendir(PHPWG_PLUGINS_PATH);
  while ($file = readdir($dir))
  {
    if ($file!='.' and $file!='..')
    {
      $path = PHPWG_PLUGINS_PATH.$file;
      if (is_dir($path) and !is_link($path)
          and preg_match('/^[a-zA-Z0-9-_]+$/', $file )
          and file_exists($path.'/index.php')
          )
      {
        $plugin = array('name'=>'?', 'version'=>'0', 'uri'=>'', 'description'=>'');
        $plg_data = implode( '', file($path.'/index.php') );

        if ( preg_match("|Plugin Name: (.*)|i", $plg_data, $val) )
        {
          $plugin['name'] = trim( $val[1] );
        }
        if (preg_match("|Version: (.*)|i", $plg_data, $val))
        {
          $plugin['version'] = trim($val[1]);
        }
        if ( preg_match("|Plugin URI: (.*)|i", $plg_data, $val) )
        {
          $plugin['uri'] = $val[1];
        }
        if ( preg_match("|Description: (.*)|i", $plg_data, $val) )
        {
          $plugin['description'] = trim($val[1]);
        }
        $plugins[$file] = $plugin;
      }
    }
  }
  closedir($dir);
  return $plugins;
}

/*allows plugins to add their content to the administration page*/
function add_plugin_admin_menu($title, $func)
{
  global $page;

  $uid = md5( var_export($func, true) );
  $page['plugin_admin_menu'][] =
    array(
      'title' => $title,
      'function' => $func,
      'uid' => $uid
    );
}

?>