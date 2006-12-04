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
define('PHPWG_ROOT_PATH','../../');
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once( 'phpBarGraph.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//------------------------------------------------ variable definition
$outputFormat = "png";
$legend = $lang['stats_daily_graph_title'];
$imageHeight = 256;
$imageWidth = 512;
$sql = '
SELECT DISTINCT COUNT(*)
     , HOUR(DATE_FORMAT(date, \'%H:%i:%s\'))
  FROM '.HISTORY_TABLE.'
  WHERE YEAR(date) = '.$_GET['year'].'
    AND MONTH(date) = '.$_GET['month'].'
    AND DAYOFMONTH(date) = '.$_GET['day'].'
  GROUP BY DATE_FORMAT(date, \'%H\') DESC;';

//------------------------------------------------ Image definition
$image = ImageCreate($imageWidth, $imageHeight);
//$image = ImageCreateTrueColor($imageWidth, $imageHeight);
// Fill it with your favorite background color..
$backgroundColor = ImageColorAllocate($image, 184, 184, 184);
ImageFill($image, 0, 0, $backgroundColor);
$white = ImageColorAllocate($image, 0, 0, 0);

// Interlace the image..
Imageinterlace($image, 1);

// Create a new BarGraph..
$myBarGraph = new PhpBarGraph;
$myBarGraph->SetX(10);              // Set the starting x position
$myBarGraph->SetY(10);              // Set the starting y position
$myBarGraph->SetWidth($imageWidth-20);    // Set how wide the bargraph will be
$myBarGraph->SetHeight($imageHeight-20);  // Set how tall the bargraph will be
$myBarGraph->SetNumOfValueTicks(3); // Set this to zero if you don't want to show any. These are the vertical bars to help see the values.


// You can try uncommenting these lines below for different looks.

// $myBarGraph->SetShowLabels(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the labels of each bar.
$myBarGraph->SetShowValues(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the values of each bar.
// $myBarGraph->SetBarBorder(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the border of each bar.
// $myBarGraph->SetShowFade(false);    // The default is true. Setting this to false will cause phpBarGraph to not print each bar as a gradient.
// $myBarGraph->SetShowOuterBox(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the outside box.
$myBarGraph->SetBarSpacing(5);     // The default is 10. This changes the space inbetween each bar.


// Add Values to the bargraph..
$result = pwg_query($sql)
or die(mysql_errno().": ".mysql_error()."<BR>".$sql);

$hours = array();
for ($i = 0; $i <= 23; $i++)
{
  $hours[$i] = 0;
}

while ($r = mysql_fetch_row($result))
{ 
  $hours[$r[1]]= $r[0];
}
$o=0;
while (list ($key,$value) = each($hours ))
{
  $myBarGraph->AddValue($key, $value);
}

//$myBarGraph->SetDebug(true);
// Set the colors of the bargraph..
$myBarGraph->SetStartBarColor("6666ff");  // This is the color on the top of every bar.
$myBarGraph->SetEndBarColor("2222aa");    // This is the color on the bottom of every bar. This is not used when SetShowFade() is set to false.
$myBarGraph->SetLineColor("000000");      // This is the color all the lines and text are printed out with.

// Print the BarGraph to the image..
$myBarGraph->DrawBarGraph($image);
Imagestring($image, 2, 2, $imageHeight-14, $legend, $white);
   
//------------------------------------------------ Image output
if ($outputFormat == "png")
{
  header("Content-type: image/png");
  ImagePNG($image);
}
else if (in_array($outputFormat, array("jpg", "jpeg")))
{
  header("Content-type: image/jpeg");
  Imagejpeg($image);
}
// Destroy the image.
Imagedestroy($image);
?> 
