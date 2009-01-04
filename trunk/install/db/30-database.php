<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Add history_guest and login_history to #config';

$query = '
INSERT INTO '.PREFIX_TABLE."config (param,value,comment) VALUES ('history_admin',".
((isset($conf['history_admin']) and $conf['history_admin']) ? 'true' : 'false').
",'keep a history of administrator visits on your website');";
pwg_query($query);

$query = '
INSERT INTO '.PREFIX_TABLE."config (param,value,comment) VALUES ('history_guest','true','keep a history of guest visits on your website');";
pwg_query($query);

$query = '
INSERT INTO '.PREFIX_TABLE."config (param,value,comment) VALUES ('login_history','true','keep a history of user logins on your website');";
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
