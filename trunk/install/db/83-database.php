<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Update column save author_id with value.';

$query = '
UPDATE
  '.COMMENTS_TABLE.' AS c ,
  '.USERS_TABLE.' AS u,
  '.USER_INFOS_TABLE.' AS i
SET c.author_id = u.'.$conf['user_fields']['id'].'
WHERE
    c.author_id is null
AND c.author = u.'.$conf['user_fields']['username'].' 
AND u.'.$conf['user_fields']['id'].' = i.user_id
AND i.registration_date <= c.date
;';

pwg_query($query);

$query = '
UPDATE '.COMMENTS_TABLE.' AS c 
SET c.author_id = '.$conf['guest_id'].'
WHERE c.author_id is null
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
