<?php
// +-----------------------------------------------------------------------+
// |                            page_header.php                            |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
 
//
// Start output of page
//
$template->set_filenames(array('header'=>'header.tpl'));

$css = './template/'.$user['template'].'/'.$user['template'].'.css';

$template->assign_vars(array(
                         'S_CONTENT_ENCODING' => $lang['charset'],
                         'T_STYLE' => $css, 
                         'PAGE_TITLE' => $title
                         ));

// refresh
if ( isset( $refresh ) && $refresh >0 && isset($url_link))
{
  $url = $url_link.'&amp;slideshow='.$refresh;
  $template->assign_vars(array(
                           'S_REFRESH_TIME' => $refresh,
                           'U_REFRESH' => add_session_id( $url )
                           ));
  $template->assign_block_vars('refresh', array());
}

// Work around for "current" Apache 2 + PHP module which seems to not
// cope with private cache control setting
if (!empty( $_SERVER['SERVER_SOFTWARE'] )
    and strstr( $_SERVER['SERVER_SOFTWARE'], 'Apache/2'))
{
  header( 'Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0' );
}
else
{
  header( 'Cache-Control: private, pre-check=0, post-check=0, max-age=0' );
}
header( 'Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT' );
header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT' );

$template->pparse('header');
$vtp=new VTemplate;
?>
