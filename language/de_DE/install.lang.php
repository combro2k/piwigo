<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'Installation';
$lang['Initial_config'] = 'Basis-Konfiguration';
$lang['Default_lang'] = 'Standardsprache der Galerie';
$lang['step1_title'] = 'Konfiguration der Datenbank';
$lang['step2_title'] = 'Konfiguration des Administrator-Kontos';
$lang['Start_Install'] = 'Start der Installation';
$lang['reg_err_mail_address'] = 'Die E-Mail-Adresse muss in der Form xxx@yyy.eee (Beispiel: jack@altern.org)';

$lang['install_webmaster'] = 'Administrator';
$lang['install_webmaster_info'] = 'Benutzername des Administrators';

$lang['step1_confirmation'] = 'Die Parameter sind korrekt ausgef�llt';
$lang['step1_err_db'] = 'Die Verbindung zum Server ist OK, aber nicht die Verbindung zu dieser Datenbank';
$lang['step1_err_server'] = 'Es konnte keine Verbindung zum Datenbankserver aufgebaut werden';

$lang['step1_host'] = 'MySQL Host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Benutzer';
$lang['step1_user_info'] = 'Benutzernamen f�r die MySQL Datenbank';
$lang['step1_pass'] = 'Passwort';
$lang['step1_pass_info'] = 'das von Ihrem Hosting-Provider';
$lang['step1_database'] = 'Name der Datenbank';
$lang['step1_database_info'] = 'Passwort f�r die MySQL Datenbank';
$lang['step1_prefix'] = 'Vorwahl Tabellen';
$lang['step1_prefix_info'] = 'die Namen der Tabellen mit diesem Pr&auml;fix (erm&ouml;glicht eine bessere Verwaltung der Datenbank)';
$lang['step2_err_login1'] = 'gib bitte einen Benutzernamen f�r den Webmaster an';
$lang['step2_err_login3'] = 'der Benutzername des Webmasters darf nicht die Zeichen \' und " enthalten';
$lang['step2_err_pass'] = 'Bitte w&auml;hlen Sie ein Passwort';
$lang['install_end_title'] = 'Installation abgeschlossen';
$lang['step2_pwd'] = 'Passwort';
$lang['step2_pwd_info'] = 'Administratorpasswort';
$lang['step2_pwd_conf'] = 'Passwort [Best&auml;tigung]';
$lang['step2_pwd_conf_info'] = 'Wiederholen Sie das eingegebene Passwort';
$lang['step1_err_copy'] = 'Kopieren Sie den rosa Text ohne die Bindestriche und f�gen Sie ihn in die Datei "include / mysql.inc.php" auf dem Webserver ein (Warnung: die Datei "mysql.inc.php" darf nur die rosa Zeichen enthalten, nicht mehr und nicht weniger)';
$lang['install_help'] = 'Brauchen Sie Hilfe? Stellen Sie Ihre Frage auf der <a href="%s"> Forum Piwigo </ a>.';
$lang['install_end_message'] = 'Die Konfiguration der Piwigo abgeschlossen ist, hier ist der n&auml;chste Schritt<br /><br />
* Gehen Sie zum Anmelden auf die Startseite: [ <a href="./identification.php">Identifizierung</a> ] und verwenden Sie die Login / Passwort f�r Webmaster<br />
* diesem Login erm&ouml;glicht Ihnen den Zugang zu den Verwaltungs-Panel und der Bilder- und Benutzerverwaltung.';
$lang['conf_mail_webmaster'] = 'Webmaster Mail-Adresse';
$lang['conf_mail_webmaster_info'] = 'Kontakt E-Mailadresse (nur f�r angemeldete Benutzer sichtbar)';
?>