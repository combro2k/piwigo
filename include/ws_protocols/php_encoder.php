<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $URL: svn+ssh://rvelices@svn.gna.org/svn/phpwebgallery/trunk/action.php $
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Rev$
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

class PwgSerialPhpEncoder extends PwgResponseEncoder
{
  function encodeResponse($response)
  {
    $respClass = strtolower( get_class($response) );
    if ($respClass=='pwgerror')
    {
      return serialize(
        array(
          'stat' => 'fail',
          'err' => $response->code(),
          'message' => $response->message(),
          )
      );
    }
    parent::flattenResponse($response);
    return serialize(
        array(
          'stat' => 'ok',
          'result' => $response
      )
    );
  }

  function getContentType()
  {
    return 'text/plain';
  }
}

?>
