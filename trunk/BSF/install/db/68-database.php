<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'Change type from text to mediumtext for #sessions.data #user_cache.forbidden_categories and #user_cache.image_access_list';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.SESSIONS_TABLE.'
  MODIFY COLUMN data MEDIUMTEXT NOT NULL';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.'
  MODIFY COLUMN forbidden_categories MEDIUMTEXT,
  MODIFY COLUMN image_access_list MEDIUMTEXT
  ';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
