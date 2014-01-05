<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
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

/**
 * Event handler for method invocation security check. Should return a PwgError
 * if the preconditions are not satifsied for method invocation.
 */
function ws_isInvokeAllowed($res, $methodName, $params)
{
  global $conf;

  if ( strpos($methodName,'reflection.')===0 )
  { // OK for reflection
    return $res;
  }

  if ( !is_autorize_status(ACCESS_GUEST) and
      strpos($methodName,'pwg.session.')!==0 )
  {
    return new PwgError(401, 'Access denied');
  }

  return $res;
}

/**
 * returns a "standard" (for our web service) array of sql where clauses that
 * filters the images (images table only)
 */
function ws_std_image_sql_filter( $params, $tbl_name='' )
{
  $clauses = array();
  if ( is_numeric($params['f_min_rate']) )
  {
    $clauses[] = $tbl_name.'rating_score>='.$params['f_min_rate'];
  }
  if ( is_numeric($params['f_max_rate']) )
  {
    $clauses[] = $tbl_name.'rating_score<='.$params['f_max_rate'];
  }
  if ( is_numeric($params['f_min_hit']) )
  {
    $clauses[] = $tbl_name.'hit>='.$params['f_min_hit'];
  }
  if ( is_numeric($params['f_max_hit']) )
  {
    $clauses[] = $tbl_name.'hit<='.$params['f_max_hit'];
  }
  if ( isset($params['f_min_date_available']) )
  {
    $clauses[] = $tbl_name."date_available>='".$params['f_min_date_available']."'";
  }
  if ( isset($params['f_max_date_available']) )
  {
    $clauses[] = $tbl_name."date_available<'".$params['f_max_date_available']."'";
  }
  if ( isset($params['f_min_date_created']) )
  {
    $clauses[] = $tbl_name."date_creation>='".$params['f_min_date_created']."'";
  }
  if ( isset($params['f_max_date_created']) )
  {
    $clauses[] = $tbl_name."date_creation<'".$params['f_max_date_created']."'";
  }
  if ( is_numeric($params['f_min_ratio']) )
  {
    $clauses[] = $tbl_name.'width/'.$tbl_name.'height>='.$params['f_min_ratio'];
  }
  if ( is_numeric($params['f_max_ratio']) )
  {
    $clauses[] = $tbl_name.'width/'.$tbl_name.'height<='.$params['f_max_ratio'];
  }
  if (is_numeric($params['f_max_level']) )
  {
    $clauses[] = $tbl_name.'level <= '.$params['f_max_level'];
  }
  return $clauses;
}

/**
 * returns a "standard" (for our web service) ORDER BY sql clause for images
 */
function ws_std_image_sql_order( $params, $tbl_name='' )
{
  $ret = '';
  if ( empty($params['order']) )
  {
    return $ret;
  }
  $matches = array();
  preg_match_all('/([a-z_]+) *(?:(asc|desc)(?:ending)?)? *(?:, *|$)/i',
    $params['order'], $matches);
  for ($i=0; $i<count($matches[1]); $i++)
  {
    switch ($matches[1][$i])
    {
      case 'date_created':
        $matches[1][$i] = 'date_creation'; break;
      case 'date_posted':
        $matches[1][$i] = 'date_available'; break;
      case 'rand': case 'random':
        $matches[1][$i] = DB_RANDOM_FUNCTION.'()'; break;
    }
    $sortable_fields = array('id', 'file', 'name', 'hit', 'rating_score',
      'date_creation', 'date_available', DB_RANDOM_FUNCTION.'()' );
    if ( in_array($matches[1][$i], $sortable_fields) )
    {
      if (!empty($ret))
        $ret .= ', ';
      if ($matches[1][$i] != DB_RANDOM_FUNCTION.'()' )
      {
        $ret .= $tbl_name;
      }
      $ret .= $matches[1][$i];
      $ret .= ' '.$matches[2][$i];
    }
  }
  return $ret;
}

/**
 * returns an array map of urls (thumb/element) for image_row - to be returned
 * in a standard way by different web service methods
 */
function ws_std_get_urls($image_row)
{
  $ret = array();

  $ret['page_url'] = make_picture_url( array(
            'image_id' => $image_row['id'],
            'image_file' => $image_row['file'],
          )
        );

  $src_image = new SrcImage($image_row);

  if ( $src_image->is_original() )
  {// we have a photo
    global $user;
    if ($user['enabled_high'])
    {
      $ret['element_url'] = $src_image->get_url();
    }
  }
  else
  {
    $ret['element_url'] = get_element_url($image_row);
  }

  $derivatives = DerivativeImage::get_all($src_image);
  $derivatives_arr = array();
  foreach($derivatives as $type=>$derivative)
  {
    $size = $derivative->get_size();
    $size != null or $size=array(null,null);
    $derivatives_arr[$type] = array('url' => $derivative->get_url(), 'width'=>$size[0], 'height'=>$size[1] );
  }
  $ret['derivatives'] = $derivatives_arr;;
  return $ret;
}

/**
 * returns an array of image attributes that are to be encoded as xml attributes
 * instead of xml elements
 */
function ws_std_get_image_xml_attributes()
{
  return array(
    'id','element_url', 'page_url', 'file','width','height','hit','date_available','date_creation'
    );
}

function ws_std_get_category_xml_attributes()
{
  return array(
    'id', 'url', 'nb_images', 'total_nb_images', 'nb_categories', 'date_last', 'max_date_last',
    );
}

function ws_std_get_tag_xml_attributes()
{
  return array(
    'id', 'name', 'url_name', 'counter', 'url', 'page_url',
    );
}

/**
 * Writes info to the log file
 */
function ws_logfile($string)
{
  global $conf;

  if (!$conf['ws_enable_log'])
  {
    return true;
  }

  file_put_contents(
    $conf['ws_log_filepath'],
    '['.date('c').'] '.$string."\n",
    FILE_APPEND
    );
}

/**
 * create a tree from a flat list of categories, no recursivity for high speed
 */
function categories_flatlist_to_tree($categories)
{
  $tree = array();
  $key_of_cat = array();

  foreach ($categories as $key => &$node)
  {
    $key_of_cat[$node['id']] = $key;

    if (!isset($node['id_uppercat']))
    {
      $tree[] = &$node;
    }
    else
    {
      if (!isset($categories[ $key_of_cat[ $node['id_uppercat'] ] ]['sub_categories']))
      {
        $categories[ $key_of_cat[ $node['id_uppercat'] ] ]['sub_categories'] =
          new PwgNamedArray(array(), 'category', ws_std_get_category_xml_attributes());
      }

      $categories[ $key_of_cat[ $node['id_uppercat'] ] ]['sub_categories']->_content[] = &$node;
    }
  }

  return $tree;
}

?>