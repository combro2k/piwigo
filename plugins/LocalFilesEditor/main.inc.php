<?php
/*
Plugin Name: LocalFiles Editor
Version: 1.0
Description: Edit local files from administration panel
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
*/
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
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

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
define('LOCALEDIT_PATH' , PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');

function localfiles_admin_menu($menu)
{
    array_push($menu, array('NAME' => 'LocalFiles Editor',
            'URL' => get_admin_plugin_menu_link(LOCALEDIT_PATH . 'admin.php')));
    return $menu;
}

add_event_handler('get_admin_plugin_menu_links', 'localfiles_admin_menu');

?>