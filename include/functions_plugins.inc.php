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

define('EVENT_HANDLER_PRIORITY_NEUTRAL', 50);

/* Register a event handler.
 * @param string $event the name of the event to listen to
 * @param mixed $func the function that will handle the event
 * @param int $priority optional priority (greater priority will
 * be executed at last)
*/
function add_event_handler($event, $func,
    $priority=EVENT_HANDLER_PRIORITY_NEUTRAL, $accepted_args=1)
{
  global $pwg_event_handlers;

  if ( isset($pwg_event_handlers[$event][$priority]) )
  {
    foreach($pwg_event_handlers[$event][$priority] as $handler)
    {
      if ( $handler['function'] == $func )
      {
        return true;
      }
    }
  }

  $pwg_event_handlers[$event][$priority][] =
    array(
      'function'=>$func,
      'accepted_args'=>$accepted_args);
  ksort( $pwg_event_handlers[$event] );
  return true;
}

/* Register a event handler.
 * @param string $event the name of the event to listen to
 * @param mixed $func the function that needs removal
 * @param int $priority optional priority (greater priority will
 * be executed at last)
*/
function remove_event_handler($event, $func,
   $priority=EVENT_HANDLER_PRIORITY_NEUTRAL)
{
  global $pwg_event_handlers;

  if (!isset( $pwg_event_handlers[$event][$priority] ) )
  {
    return false;
  }
  for ($i=0; $i<count($pwg_event_handlers[$event][$priority]); $i++)
  {
    if ($pwg_event_handlers[$event][$priority][$i]['function']==$func)
    {
      unset($pwg_event_handlers[$event][$priority][$i]);
      $pwg_event_handlers[$event][$priority] =
        array_values($pwg_event_handlers[$event][$priority]);

      if ( empty($pwg_event_handlers[$event][$priority]) )
      {
        unset( $pwg_event_handlers[$event][$priority] );
        if (empty( $pwg_event_handlers[$event] ) )
        {
          unset( $pwg_event_handlers[$event] );
        }
      }
      return true;
    }
  }
  return false;
}

/* Triggers an event and calls all registered event handlers
 * @param string $event name of the event
 * @param mixed $data data to pass to handlers
*/
function trigger_event($event, $data=null)
{
  global $pwg_event_handlers;

  // just for debugging
  trigger_action('pre_trigger_event',
        array('event'=>$event, 'data'=>$data) );

  if ( !isset($pwg_event_handlers[$event]) )
  {
    trigger_action('post_trigger_event',
        array('event'=>$event, 'data'=>$data) );
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
  trigger_action('post_trigger_event',
        array('event'=>$event, 'data'=>$data) );
  return $data;
}


function trigger_action($event, $data=null)
{
  global $pwg_event_handlers;
  if ($event!='pre_trigger_event'
    and $event!='post_trigger_event'
    and $event!='trigger_action')
  {// special case for debugging - avoid recursive calls
    trigger_action('trigger_action',
        array('event'=>$event, 'data'=>$data) );
  }

  if ( !isset($pwg_event_handlers[$event]) )
  {
    return;
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

        call_user_func_array($function_name, $the_args);
      }
    }
  }
}


/* Returns an array of plugins defined in the database
 * @param string $state optional filter on this state
 * @param string $id optional returns only data about given plugin
*/
function get_db_plugins($state='', $id='')
{
  $query = '
SELECT * FROM '.PLUGINS_TABLE;
  if (!empty($state) or !empty($id) )
  {
    $query .= '
WHERE 1=1';
    if (!empty($state))
    {
      $query .= '
  AND state="'.$state.'"';
    }
    if (!empty($id))
    {
      $query .= '
  AND id="'.$id.'"';
    }
  }

  $result = pwg_query($query);
  $plugins = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($plugins, $row);
  }
  return $plugins;
}


/*loads all the plugins on startup*/
function load_plugins()
{
  global $conf;
  if ($conf['disable_plugins'])
  {
    return;
  }

  $plugins = get_db_plugins('active');
  foreach( $plugins as $plugin)
  {
    $file_name = PHPWG_PLUGINS_PATH.$plugin['id'].'/main.inc.php';
    if ( file_exists($file_name) )
    {
      include_once( $file_name );
    }
  }
  trigger_action('plugins_loaded');
}
?>