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

/*
Events and event handlers are the core of PhpWebGallery plugin management.
Plugins are addons that are found in plugins subdirectory. If activated, PWG
will include the index.php of each plugin.
Events are triggered by PWG core code. Plugins (or even PWG itself) can
register their functions to handle these events. An event is identified by a
string.
*/

define('PHPWG_PLUGINS_PATH',PHPWG_ROOT_PATH.'plugins/');

/* Register a event handler.
 * @param string $event the name of the event to listen to
 * @param mixed $func the function that will handle the event
*/
function add_event_handler($event, $func, $priority=50, $accepted_args=1)
{
  global $pwg_event_handlers;

  if ( isset($pwg_event_handlers[$event]["$priority"]) )
  {
    foreach($pwg_event_handlers[$event]["$priority"] as $handler)
    {
      if ( $handler['function'] == $func )
      {
        return true;
      }
    }
  }

  trigger_event('add_event_handler',
      array('event'=>$event, 'function'=>$func)
    );

  $pwg_event_handlers[$event]["$priority"][] =
    array(
      'function'=>$func,
      'accepted_args'=>$accepted_args);

  return true;
}


function trigger_event($event, $data=null)
{
  global $pwg_event_handlers;
  if ($event!='pre_trigger_event' and $event!='post_trigger_event')
  {// special case
    trigger_event('pre_trigger_event',
        array('event'=>$event, 'data'=>$data) );
    if ( !isset($pwg_event_handlers[$event]) )
    {
      trigger_event('post_trigger_event',
          array('event'=>$event, 'data'=>$data) );
    }
  }

  if ( !isset($pwg_event_handlers[$event]) )
  {
    return $data;
  }
  $args = array_slice(func_get_args(), 2);

  foreach ($pwg_event_handlers[$event] as $priority => $handlers)
  {
    if ( !is_null($handlers) )
    {
      foreach($handlers as $handler)
      {
        $all_args = array_merge( array($data), $args);
        $function_name = $handler['function'];
        $accepted_args = $handler['accepted_args'];

        if ( $accepted_args == 1 )
          $the_args = array($data);
        elseif ( $accepted_args > 1 )
          $the_args = array_slice($all_args, 0, $accepted_args);
        elseif ( $accepted_args == 0 )
          $the_args = NULL;
        else
          $the_args = $all_args;

        $data = call_user_func_array($function_name, $the_args);
      }
    }
  }

  if ($event!='pre_trigger_event' and $event!='post_trigger_event')
  {
    trigger_event('post_trigger_event',
        array('event'=>$event, 'data'=>$data) );
  }

  return $data;
}





function get_active_plugins($runtime = true)
{
  global $conf;
  if ($conf['disable_plugins'] and $runtime)
  {
    return array();
  }
  if (empty($conf['active_plugins']))
  {
    return array();
  }
  return explode(',', $conf['active_plugins']);

}


function load_plugins()
{
  $plugins = get_active_plugins();
  foreach( $plugins as $plugin)
  {
    if (!empty($plugin))
    {
      include_once( PHPWG_PLUGINS_PATH.$plugin.'/index.php' );
    }
  }
  trigger_event('plugins_loaded');
}
?>