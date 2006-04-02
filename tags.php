<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-22 02:01:47 +0100 (mer, 22 mar 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1092 $
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

// +-----------------------------------------------------------------------+
// |                             functions                                 |
// +-----------------------------------------------------------------------+

function counter_compare($a, $b)
{
  if ($a['counter'] == $b['counter'])
  {
    return tag_id_compare($a, $b);
  }

  return ($a['counter'] < $b['counter']) ? -1 : 1;
}

function tag_id_compare($a, $b)
{
  return ($a['tag_id'] < $b['tag_id']) ? -1 : 1;
}

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

check_status(ACCESS_GUEST);

// +-----------------------------------------------------------------------+
// |                       page header and options                         |
// +-----------------------------------------------------------------------+

$title= l10n('Tags');
$page['body_id'] = 'theTagsPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('tags'=>'tags.tpl'));
$template->assign_vars(
  array(
    'U_HOME' => make_index_url(),
    )
  );

// +-----------------------------------------------------------------------+
// |                        tag cloud construction                         |
// +-----------------------------------------------------------------------+

// find all tags available for the current user
$tags = get_available_tags(explode(',', $user['forbidden_categories']));

// we want only the first most represented tags, so we sort them by counter
// and take the first tags
usort($tags, 'counter_compare');
$tags = array_slice($tags, 0, $conf['full_tag_cloud_items_number']);

// depending on its counter and the other tags counter, each tag has a level
$tags = add_level_to_tags($tags);

// we want tags diplayed in alphabetic order
usort($tags, 'name_compare');

// display sorted tags
foreach ($tags as $tag)
{
  $template->assign_block_vars(
    'tag',
    array(
      'URL' => make_index_url(
        array(
          'tags' => array(
            array(
              'id' => $tag['tag_id'],
              'url_name' => $tag['url_name'],
              ),
            ),
          )
        ),
      
      'NAME' => $tag['name'],
      'TITLE' => $tag['counter'],
      'CLASS' => 'tagLevel'.$tag['level'],
      )
    );
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_block_vars('title',array());
$template->parse('tags');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>