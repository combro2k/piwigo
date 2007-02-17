<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['about_page_title'];
$page['body_id'] = 'theAboutPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

/**
 * set in ./language/en_UK.iso-8859-1/local.lang.php (maybe to create)
 * for example for clear theme:
  $lang['Theme: clear'] = 'This is the clear theme based on yoga template. '.
  ' A standard template/theme of PhpWebgallery.';
 *
 * Don't forget php tags !!!
 *
 * Another way is to code it thru the theme itself in ./themeconf.inc.php
 */
@include(PHPWG_ROOT_PATH.'template/'.$user['template'].
  '/theme/'.$user['theme'].'/themeconf.inc.php');

$template->set_filenames(
  array(
    'about'=>'about.tpl',
    'about_content' => get_language_filepath('about.html')
    )
  );
if ( isset($lang['Theme: '.$user['theme']]) )
{
  $template->assign_block_vars(
  'theme',
  array(
    'ABOUT' => l10n('Theme: '.$user['theme']),
    )
  );
}
$template->assign_vars(
  array(
    'U_HOME' => make_index_url(),
    )
  );

$template->assign_var_from_handle('ABOUT_MESSAGE', 'about_content');
  
$template->parse('about');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
