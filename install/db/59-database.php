<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id: 58-database.php 1912 2007-03-16 06:30:07Z rub $
// | last update   : $Date: 2007-03-16 07:30:07 +0100 (ven, 16 mar 2007) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1912 $
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

$upgrade_description = 'Rename some indexes following PWG naming rules';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = "
DROP INDEX image_category ON ".IMAGE_CATEGORY_TABLE."
;
";
pwg_query($query);

$query = "
CREATE INDEX image_category_i1 ON ".IMAGE_CATEGORY_TABLE." (category_id)
;
";
pwg_query($query);

$query = "
DROP INDEX uidx_check_key ON ".USER_MAIL_NOTIFICATION_TABLE."
;
";
pwg_query($query);

$query = "
CREATE UNIQUE INDEX user_mail_notification_ui1 ON ".USER_MAIL_NOTIFICATION_TABLE." (check_key)
;
";
pwg_query($query);

$query = "
DROP INDEX name ON ".WEB_SERVICES_ACCESS_TABLE."
;
";
pwg_query($query);

$query = "
CREATE UNIQUE INDEX ws_access_ui1 ON ".WEB_SERVICES_ACCESS_TABLE." (name)
;
";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>