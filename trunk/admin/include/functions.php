<?php
// +-----------------------------------------------------------------------+
// |                             functions.php                             |
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

$tab_ext_create_TN = array ( 'jpg', 'png', 'JPG', 'PNG' );

// is_image returns true if the given $filename (including the path) is a
// picture according to its format and its extension.
// As GD library can only generate pictures from jpeg and png files, if you
// ask if the filename is an image for thumbnail creation (second parameter
// set to true), the only authorized formats are jpeg and png.
function is_image( $filename, $create_thumbnail = false )
{
  global $conf, $tab_ext_create_TN;

  if ( is_file( $filename ) )
  {
    $size = getimagesize( $filename );
    // $size[2] == 1 means GIF
    // $size[2] == 2 means JPG
    // $size[2] == 3 means PNG
    if ( !$create_thumbnail )
    {
      if ( in_array( get_extension( $filename ), $conf['picture_ext'] )
           and ( $size[2] == 1 or $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
    else
    {
      if ( in_array( get_extension( $filename ), $tab_ext_create_TN )
           and ( $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
  }
  return false;
}

/**
 * returns an array with all picture files according to $conf['picture_ext']
 *
 * @param string $dir
 * @return array
 */
function get_picture_files( $dir )
{
  global $conf;

  $pictures = array();
  if ( $opendir = opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( in_array( get_extension( $file ), $conf['picture_ext'] ) )
      {
        array_push( $pictures, $file );
      }
    }
  }
  return $pictures;
}

/**
 * returns an array with all thumbnails according to $conf['picture_ext']
 * and $conf['prefix_thumbnail']
 *
 * @param string $dir
 * @return array
 */
function get_thumb_files( $dir )
{
  global $conf;

  $prefix_length = strlen( $conf['prefix_thumbnail'] );
  
  $thumbnails = array();
  if ( $opendir = @opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( in_array( get_extension( $file ), $conf['picture_ext'] )
           and substr($file,0,$prefix_length) == $conf['prefix_thumbnail'] )
      {
        array_push( $thumbnails, $file );
      }
    }
  }
  return $thumbnails;
}

function TN_exists( $dir, $file )
{
  global $conf;

  $filename = get_filename_wo_extension( $file );
  foreach ( $conf['picture_ext'] as $ext ) {
    $test = $dir.'/thumbnail/'.$conf['prefix_thumbnail'].$filename.'.'.$ext;
    if ( is_file ( $test ) )
    {
      return $ext;
    }
  }
  return false;
}
	

// The function delete_site deletes a site
// and call the function delete_category for each primary category of the site
function delete_site( $id )
{
  // destruction of the categories of the site
  $query = 'SELECT id';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE site_id = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    delete_category( $row['id'] );
  }
		
  // destruction of the site
  $query = 'DELETE FROM '.PREFIX_TABLE.'sites';
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
}
	

// The function delete_category deletes the category identified by the $id
// It also deletes (in the database) :
//    - all the images of the images (thanks to delete_image, see further)
//    - all the links between images and this category
//    - all the restrictions linked to the category
// The function works recursively.
function delete_category( $id )
{
  // destruction of all the related images
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    delete_image( $row['id'] );
  }

  // destruction of the links between images and this category
  $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
  $query.= ' WHERE category_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the access linked to the category
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE cat_id = '.$id;
  $query.= ';';
  mysql_query( $query );
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE cat_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the sub-categories
  $query = 'SELECT id';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id_uppercat = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    delete_category( $row['id'] );
  }

  // destruction of the category
  $query = 'DELETE FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
}
	

// The function delete_image deletes the image identified by the $id
// It also deletes (in the database) :
//    - all the comments related to the image
//    - all the links between categories and this image
//    - all the favorites associated to the image
function delete_image( $id )
{
  global $count_deleted;
		
  // destruction of the comments on the image
  $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the links between images and this category
  $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the favorites associated with the picture
  $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );
		
  // destruction of the image
  $query = 'DELETE FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
  $count_deleted++;
}

// The delete_user function delete a user identified by the $user_id
// It also deletes :
//     - all the access linked to this user
//     - all the links to any group
//     - all the favorites linked to this user
//     - all sessions linked to this user
//     - all categories informations linked to this user
function delete_user( $user_id )
{
  // destruction of the access linked to the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the group links for this user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the favorites associated with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the sessions linked with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'sessions';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the user
  $query = 'DELETE FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  mysql_query( $query );
}

// delete_group deletes a group identified by its $group_id.
// It also deletes :
//     - all the access linked to this group
//     - all the links between this group and any user
function delete_group( $group_id )
{
  // destruction of the access linked to the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  mysql_query( $query );

  // synchronize all users linked to the group
  synchronize_group( $group_id );

  // destruction of the users links for this group
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'groups';
  $query.= ' WHERE id = '.$group_id;
  $query.= ';';
  mysql_query( $query );
}

// The check_favorites function deletes all the favorites of a user if he is
// not allowed to see them (the category or an upper category is restricted
// or invisible)
function check_favorites( $user_id )
{
  $query = 'SELECT status,forbidden_categories';
  $query.= ' FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $status = $row['status'];
  // retrieving all the restricted categories for this user
  if ( isset( $row['forbidden_categories'] ) )
    $restricted_cat = explode( ',', $row['forbidden_categories'] );
  else
    $restricted_cat = array();
  // retrieving all the favorites for this user and comparing their
  // categories to the restricted categories
  $query = 'SELECT image_id';
  $query.= ' FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = mysql_query ( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // for each picture, we have to check all the categories it belongs
    // to. Indeed if a picture belongs to category_1 and category_2 and that
    // category_2 is not restricted to the user, he can have the picture as
    // favorite.
    $query = 'SELECT DISTINCT(category_id) as category_id';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ';';
    $picture_result = mysql_query( $query );
    $picture_cat = array();
    while ( $picture_row = mysql_fetch_array( $picture_result ) )
    {
      array_push( $picture_cat, $picture_row['category_id'] );
    }
    if ( count( array_diff( $picture_cat, $restricted_cat ) ) == 0 )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
      $query.= ' WHERE image_id = '.$row['image_id'];
      $query.= ' AND user_id = '.$user_id;
      $query.= ';';
      mysql_query( $query );
    }
  }
}

// update_category updates calculated informations about a category :
// date_last and nb_images. It also verifies that the representative picture
// is really linked to the category.
function update_category( $id = 'all' )
{
  if ( $id == 'all' )
  {
    $query = 'SELECT id FROM '.CATEGORIES_TABLE.';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      // recursive call
      update_category( $row['id'] );
    }
  }
  else if ( is_numeric( $id ) )
  {
    // updating the number of pictures
    $query = 'SELECT COUNT(*) as nb_images';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$id;
    $query.= ';';
    list( $nb_images ) = mysql_fetch_array( mysql_query( $query ) );
    // updating the date_last
    $query = 'SELECT MAX(date_available) AS date_available';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category ON id = image_id';
    $query.= ' WHERE category_id = '.$id;
    $query.= ';';
    list( $date_available ) = mysql_fetch_array( mysql_query( $query ) );
    
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= " SET date_last = '".$date_available."'";
    $query.= ', nb_images = '.$nb_images;
    $query.= ' WHERE id = '.$id;
    $query.= ';';
    mysql_query( $query );

    // updating the representative_picture_id : if the representative
    // picture of the category is not any more linked to the category, we
    // have to set representative_picture_id to NULL
    $query = 'SELECT representative_picture_id';
    $query.= ' FROM '.CATEGORIES_TABLE;
    $query.= ' WHERE id = '.$id;
    $row = mysql_fetch_array( mysql_query( $query ) );
    // if the category has no representative picture (ie
    // representative_picture_id == NULL) we don't update anything
    if ( isset( $row['representative_picture_id'] ) )
    {
      $query = 'SELECT image_id';
      $query.= ' FROM '.PREFIX_TABLE.'image_category';
      $query.= ' WHERE category_id = '.$id;
      $query.= ' AND image_id = '.$row['representative_picture_id'];
      $query.= ';';
      $result = mysql_query( $query );
      if ( mysql_num_rows( $result ) == 0 )
      {
        $query = 'UPDATE '.CATEGORIES_TABLE;
        $query.= ' SET representative_picture_id = NULL';
        $query.= ' WHERE id = '.$id;
        $query.= ';';
        mysql_query( $query );
      }
    }
  }
}

function check_date_format( $date )
{
  // date arrives at this format : DD/MM/YYYY
  @list($day,$month,$year) = explode( '/', $date );
  return @checkdate( $month, $day, $year );
}

function date_convert( $date )
{
  // date arrives at this format : DD/MM/YYYY
  // It must be transformed in YYYY-MM-DD
  list($day,$month,$year) = explode( '/', $date );
  return $year.'-'.$month.'-'.$day;
}

function date_convert_back( $date )
{
  // date arrives at this format : YYYY-MM-DD
  // It must be transformed in DD/MM/YYYY
  if ( $date != '' )
  {
    list($year,$month,$day) = explode( '-', $date );
    return $day.'/'.$month.'/'.$year;
  }
  else
  {
    return '';
  }
}

// get_keywords returns an array with relevant keywords found in the string
// given in argument. Keywords must be separated by comma in this string.
// keywords must :
//   - be longer or equal to 3 characters
//   - not contain ', " or blank characters
//   - unique in the string ("test,test" -> "test")
function get_keywords( $keywords_string )
{
  $keywords = array();

  $candidates = explode( ',', $keywords_string );
  foreach ( $candidates as $candidate ) {
    if ( strlen($candidate) >= 3 and !preg_match( '/(\'|"|\s)/', $candidate ) )
      array_push( $keywords, $candidate );
  }

  return array_unique( $keywords );
}

function display_categories( $categories, $indent,
                             $selected = -1, $forbidden = -1 )
{
  global $vtp,$sub;

  foreach ( $categories as $category ) {
    if ( $category['id'] != $forbidden )
    {
      $vtp->addSession( $sub, 'associate_cat' );
      $vtp->setVar( $sub, 'associate_cat.value',   $category['id'] );
      $content = $indent.'- '.$category['name'];
      $vtp->setVar( $sub, 'associate_cat.content', $content );
      if ( $category['id'] == $selected )
        $vtp->setVar( $sub, 'associate_cat.selected', ' selected="selected"' );
      $vtp->closeSession( $sub, 'associate_cat' );
      display_categories( $category['subcats'], $indent.str_repeat('&nbsp;',3),
                          $selected, $forbidden );
    }
  }
}

/**
 * Complete plain structure of the gallery
 *
 * Returns the plain structure (one level array) of the gallery. In the
 * returned array, each element is an array with jeys 'id' and
 * 'id_uppercat'. The function also fills the array $page['subcats'] which
 * associate (category_id => array of sub-categories id).
 *
 * @param bool $use_name
 * @return array
 */
function get_plain_structure( $use_name = false )
{
  global $page;

  $plain_structure = array();

  $query = 'SELECT id,id_uppercat';
  if ( $use_name ) $query.= ',name';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' ORDER BY id_uppercat ASC, rank ASC';
  $query.= ';';

  $subcats = array();
  $id_uppercat = 'NULL';

  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $plain_structure[$row['id']]['id'] = $row['id'];
    if ( !isset( $row['id_uppercat'] ) ) $row['id_uppercat'] = 'NULL';
    $plain_structure[$row['id']]['id_uppercat'] = $row['id_uppercat'];
    if ( $use_name ) $plain_structure[$row['id']]['name'] = $row['name'];
    // subcats list
    if ( $row['id_uppercat'] != $id_uppercat )
    {
      $page['subcats'][$id_uppercat] = $subcats;

      $subcats = array();
      $id_uppercat = $row['id_uppercat'];
    }
    array_push( $subcats, $row['id'] );
  }
  mysql_free_result( $result );
  
  $page['subcats'][$id_uppercat] = $subcats;

  return $plain_structure;
}

/**
 * get N levels array representing structure under the given category
 *
 * create_structure returns the N levels array representing structure under
 * the given gategory id. It also updates the
 * $page['plain_structure'][id]['all_subcats_id'] and
 * $page['plain_structure'][id]['direct_subcats_ids'] for each sub category.
 *
 * @param int $id_uppercat
 * @return array
 */
function create_structure( $id_uppercat )
{
  global $page;

  $structure = array();
  $ids = get_subcats_ids( $id_uppercat );
  foreach ( $ids as $id ) {
    $category = $page['plain_structure'][$id];

    $category['subcats'] = create_structure( $id );

    $page['plain_structure'][$id]['all_subcats_ids'] =
      get_all_subcats_ids( $id );

    $page['plain_structure'][$id]['direct_subcats_ids'] =
      get_subcats_ids( $id );

    array_push( $structure, $category );
  }
  return $structure;
}

/**
 * returns direct sub-categories ids
 *
 * Returns an array containing all the direct sub-categories ids of the
 * given category. It uses the $page['subcats'] global array.
 *
 * @param int $id_uppercat
 * @return array
 */
function get_subcats_ids( $id_uppercat )
{
  global $page;

  if ( $id_uppercat == '' ) $id_uppercat = 'NULL';

  if ( isset( $page['subcats'][$id_uppercat] ) )
    return $page['subcats'][$id_uppercat];
  else
    return array();
}

/**
 * returns all sub-categories ids, not only direct ones
 *
 * Returns an array containing all the sub-categories ids of the given
 * category, not only direct ones. This function is recursive.
 *
 * @param int $category_id
 * @return array
 */
function get_all_subcats_ids( $category_id )
{
  $ids = array();
  
  $subcats = get_subcats_ids( $category_id );
  $ids = array_merge( $ids, $subcats );
  foreach ( $subcats as $subcat ) {
    // recursive call
    $sub_subcats = get_all_subcats_ids( $subcat );
    $ids = array_merge( $ids, $sub_subcats );
  }
  return array_unique( $ids );
}

/**
 * updates the column categories.uppercats
 *
 * @param int $category_id
 * @return void
 */
function update_uppercats( $category_id )
{
  global $page;

  $final_id = $category_id;
  $uppercats = array();

  array_push( $uppercats, $category_id );
  $uppercat = $page['plain_structure'][$category_id]['id_uppercat'];

  while ( $uppercat != 'NULL' )
  {
    array_push( $uppercats, $uppercat );
    $category_id = $page['plain_structure'][$category_id]['id_uppercat'];
    $uppercat = $page['plain_structure'][$category_id]['id_uppercat'];
  }

  $string_uppercats = implode( ',', array_reverse( $uppercats ) );
  $query = 'UPDATE '.CATEGORIES_TABLE;
  $query.= ' SET uppercats = '."'".$string_uppercats."'";
  $query.= ' WHERE id = '.$final_id;
  $query.= ';';
  mysql_query( $query );
}

/**
 * returns an array with the ids of the restricted categories for the user
 *
 * Returns an array with the ids of the restricted categories for the
 * user. If the $check_invisible parameter is set to true, invisible
 * categorie are added to the restricted one in the array.
 *
 * @param int $user_id
 * @param string $user_status
 * @param bool $check_invisible
 * @param bool $use_groups
 * @return array
 */
function get_user_restrictions( $user_id, $user_status,
                                $check_invisible, $use_groups = true )
{
  // 1. retrieving ids of private categories
  $query = 'SELECT id FROM '.CATEGORIES_TABLE;
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = mysql_query( $query );
  $privates = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $privates, $row['id'] );
  }
  // 2. retrieving all authorized categories for the user
  $authorized = array();
  // 2.1. retrieving authorized categories thanks to personnal user
  //      authorization
  $query = 'SELECT cat_id FROM '.USER_ACCESS_TABLE;
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $authorized, $row['cat_id'] );
  }
  // 2.2. retrieving authorized categories thanks to group authorization to
  //      which the user is a member
  if ( $use_groups )
  {
    $query = 'SELECT ga.cat_id';
    $query.= ' FROM '.USER_GROUP_TABLE.' as ug';
    $query.= ', '.GROUP_ACCESS_TABLE.' as ga';
    $query.= ' WHERE ug.group_id = ga.group_id';
    $query.= ' AND ug.user_id = '.$user_id;
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      array_push( $authorized, $row['cat_id'] );
    }
    $authorized = array_unique( $authorized );
  }

  $forbidden = array();
  foreach ( $privates as $private ) {
    if ( !in_array( $private, $authorized ) )
    {
      array_push( $forbidden, $private );
    }
  }

  if ( $check_invisible )
  {
    // 3. adding to the restricted categories, the invisible ones
    if ( $user_status != 'admin' )
    {
      $query = 'SELECT id FROM '.CATEGORIES_TABLE;
      $query.= " WHERE visible = 'false';";
      $result = mysql_query( $query );
      while ( $row = mysql_fetch_array( $result ) )
      {
        array_push( $forbidden, $row['id'] );
      }
    }
  }
  return array_unique( $forbidden );
}

/**
 * updates the calculated data users.forbidden_categories, it includes
 * sub-categories of the direct forbidden categories
 *
 * @param nt $user_id
 * @return array
 */
function update_user_restrictions( $user_id )
{
  $restrictions = get_user_all_restrictions( $user_id );

  // update the users.forbidden_categories in database
  $query = 'UPDATE '.USERS_TABLE;
  $query.= ' SET forbidden_categories = ';
  if ( count( $restrictions ) > 0 )
    $query.= "'".implode( ',', $restrictions )."'";
  else
    $query.= 'NULL';
  $query .= ' WHERE id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  return $restrictions;
}

/**
 * returns all the restricted categories ids including sub-categories
 *
 * @param int $user_id
 * @return array
 */
function get_user_all_restrictions( $user_id )
{
  global $page;
  
  $query = 'SELECT status';
  $query.= ' FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  
  $base_restrictions=get_user_restrictions($user_id,$row['status'],true,true);

  $restrictions = $base_restrictions;
  foreach ( $base_restrictions as $category_id ) {
    echo $category_id.' is forbidden to user '.$user_id.'<br />';
    $restrictions =
      array_merge( $restrictions,
                   $page['plain_structure'][$category_id]['all_subcats_ids'] );
  }

  return array_unique( $restrictions );
}

// The function is_user_allowed returns :
//      - 0 : if the category is allowed with this $restrictions array
//      - 1 : if this category is not allowed
//      - 2 : if an uppercat category is not allowed
// Note : the restrictions array must represent ONLY direct forbidden
// categories, not all forbidden categories
function is_user_allowed( $category_id, $restrictions )
{
  if ( in_array( $category_id, $restrictions ) ) return 1;

  $query = 'SELECT uppercats';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id = '.$category_id;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $uppercats = explode( ',', $row['uppercats'] );
  foreach ( $uppercats as $category_id ) {
    if ( in_array( $category_id, $restrictions ) ) return 2;
  }

  // no restriction found : the user is allowed to access this category
  return 0;
}

/**
 * returns an array containing sub-directories which can be a category
 *
 * directories nammed "thumbnail" are omitted
 *
 * @param string $basedir
 * @return array
 */
function get_category_directories( $basedir )
{
  $sub_dirs = array();

  if ( $opendir = opendir( $basedir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( $file != '.' and $file != '..'
           and is_dir( $basedir.'/'.$file )
           and $file != 'thumbnail' )
      {
        array_push( $sub_dirs, $file );
      }
    }
  }
  return $sub_dirs;
}
?>
