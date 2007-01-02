<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-04-21 23:16:37 +0200 (ven., 21 avr. 2006) $
// | last modifier : $Author: nikrou $
// | revision      : $Revision: 1250 $
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
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

/*$action = (isset($_GET['action']) and !is_adviser()) ? $_GET['action'] : '';

switch ($action)
{
  case '???' :
  {
    break;
  }
  default :
  {
    break;
  }
}*/

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('advanced_feature'=>'admin/advanced_feature.tpl'));

$start_url = PHPWG_ROOT_PATH.'admin.php?page=advanced_feature&amp;action=';

$template->assign_vars(
  array(
//    'U_ADV_????' => $start_url.'???',
    'U_ADV_ELEMENT_NOT_LINKED' => PHPWG_ROOT_PATH.'admin.php?page=element_set&cat=not_linked',
    'U_ADV_DUP_FILES' => PHPWG_ROOT_PATH.'admin.php?page=element_set&cat=duplicates',
    'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=advanced_feature'
    )
  );

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'advanced_feature');
?>
