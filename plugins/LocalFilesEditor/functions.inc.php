<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

/**
 * returns $code if php syntax is correct
 * else return false
 *
 * @param string php code
 */
function eval_syntax($code)
{
    $code = str_replace(array('<?php', '?>'), '', $code);
    $b = 0;
    foreach (token_get_all($code) as $token)
	{
        if ('{' == $token) ++$b;
        else if ('}' == $token) --$b;
    }
    if ($b) return false;
    else
	{
        ob_start();
        $eval = eval('if(0){' . $code . '}');
        ob_end_clean();
        if ($eval === false) return false;
        else return '<?php' . $code . '?>';
    }
}

/**
 * returns true or false if $str is bool
  * returns $str if $str is integer
 * else "$str"
 *
 * @param string
 */
function editarea_quote($value)
{
  switch (gettype($value))
  {
    case "boolean":
      return $value ? 'true' : 'false';
    case "integer":
      return $value;
    default:
      return '"'.$value.'"';
  }
}

?>