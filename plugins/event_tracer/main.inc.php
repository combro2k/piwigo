<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 Piwigo team    http://phpwebgallery.net |
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

/*
Plugin Name: Event tracer
Version: 1.8.a
Description: For developers. Shows all calls to trigger_event.
Plugin URI: http://piwigo.org
Author: Piwigo team
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

class EventTracer
{
  var $me_working;
  var $my_config;

  function EventTracer()
  {
    $this->me_working=0;
  }

  function get_config_file_dir()
  {
    global $conf;
    return $conf['local_data_dir'].'/plugins/';
  }

  function get_config_file_name()
  {
    return basename(dirname(__FILE__)).'.dat';
  }

  function load_config()
  {
    $x = @file_get_contents( $this->get_config_file_dir().$this->get_config_file_name() );
    if ($x!==false)
    {
      $c = unserialize($x);
      // do some more tests here
      $this->my_config = $c;
    }
    if ( !isset($this->my_config)
        or empty($this->my_config['filters']) )
    {
      $this->my_config['filters'] = array( '.*' );
      $this->my_config['show_args'] = false;
      $this->save_config();
    }
  }

  function save_config()
  {
    $dir = $this->get_config_file_dir();
    @mkgetdir($dir);
    $file = fopen( $dir.$this->get_config_file_name(), 'w' );
    fwrite($file, serialize($this->my_config) );
    fclose( $file );
  }

  function on_pre_trigger_event($event_info)
  {
    $this->dump('pre_trigger_event', $event_info);
  }
  function on_post_trigger_event($event_info)
  {
    $this->dump('post_trigger_event', $event_info);
  }

  function on_trigger_action($event_info)
  {
    $this->dump('trigger_action', $event_info);
  }

  function dump($event, $event_info)
  {
    foreach( $this->my_config['filters'] as $filter)
    {
      if ( preg_match( '/'.$filter.'/', $event_info['event'] ) )
      {
        if ($this->my_config['show_args'])
        {
          $s = '<pre>';
          $s .= htmlspecialchars( var_export( $event_info['data'], true ) );
          $s .= '</pre>';
        }
        else
          $s = '';
        pwg_debug($event.' "'.$event_info['event'].'" '.($s) );
        break;
      }
    }
  }

  function plugin_admin_menu($menu)
  {
    array_push($menu,
        array(
          'NAME' => 'Event Tracer',
          'URL' => get_admin_plugin_menu_link(dirname(__FILE__).'/tracer_admin.php')
        )
      );
    return $menu;
  }
}

$obj = new EventTracer();
$obj->load_config();

add_event_handler('get_admin_plugin_menu_links', array(&$obj, 'plugin_admin_menu') );
add_event_handler('pre_trigger_event', array(&$obj, 'on_pre_trigger_event') );
add_event_handler('post_trigger_event', array(&$obj, 'on_post_trigger_event') );
add_event_handler('trigger_action', array(&$obj, 'on_trigger_action') );
set_plugin_data($plugin['id'], $obj);
?>
