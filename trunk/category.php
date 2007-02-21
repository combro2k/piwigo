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
This file contains now only code to ensure backward url compatibility with
versions before 1.6
*/

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

$url_params=array();
if ( isset($_GET['cat']) )
{
  if ( is_numeric($_GET['cat']) )
  {
    $url_params['section'] = 'categories';
    $url_params['category'] = $_GET['cat'];
    $result = get_cat_info($url_params['category']);
    if ( !empty($result) )
      $url_params['cat_name'] = $result['name'];
  }
  elseif ( in_array($_GET['cat'],
              array('best_rated','most_visited','recent_pics','recent_cats')
                  )
         )
  {
    $url_params['section'] = $_GET['cat'];
  }
  else
  {
    page_not_found('');
  }
}

$url = make_index_url($url_params);
if (!headers_sent())
{
  set_status_header(302);
  redirect_http( $url );
}
redirect ( $url );

?>