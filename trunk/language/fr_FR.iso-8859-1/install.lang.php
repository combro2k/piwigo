<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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

$lang['install_message'] = 'Message';
$lang['Initial_config'] = 'Configuration de Base';
$lang['Default_lang'] = 'Langue par d�faut de la galerie';
$lang['step1_title'] = 'Configuration de la Base de donn�es';
$lang['step2_title'] = 'Configuration du compte Administrateur';
$lang['Start_Install'] = 'D�marrer l\'installation';
$lang['reg_err_mail_address'] = 'L\'adresse mail doit �tre de la forme xxx@yyy.eee (exemple : jack@altern.org)';

$lang['install_webmaster'] = 'Administrateur';
$lang['install_webmaster_info'] = 'Cet identifiant appara�tra � tous vos visiteurs. Il vous sert pour administrer le site';

$lang['step1_confirmation'] = 'Les param�tres rentr�s sont corrects';
$lang['step1_err_db'] = 'La connexion au serveur est OK, mais impossible de se connecter � cette base de donn�es';
$lang['step1_err_server'] = 'Impossible de se connecter au serveur';

$lang['step1_host'] = 'H�te MySQL';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Utilisateur';
$lang['step1_user_info'] = 'nom d\'utilisateur pour votre h�bergeur';
$lang['step1_pass'] = 'Mot de passe';
$lang['step1_pass_info'] = 'celui fourni par votre h�bergeur';
$lang['step1_database'] = 'Nom de la base';
$lang['step1_database_info'] = 'celui fourni par votre h�bergeur';
$lang['step1_prefix'] = 'Pr�fixe des noms de table';
$lang['step1_prefix_info'] = 'le nom des tables appara�tra avec ce pr�fixe (permet de mieux g�rer sa base de donn�es)';
$lang['step2_err_login1'] = 'veuillez rentrer un pseudo pour le webmaster';
$lang['step2_err_login3'] = 'le pseudo du webmaster ne doit pas comporter les caract�re " et \'';
$lang['step2_err_pass'] = 'veuillez retaper votre mot de passe';
$lang['install_end_title'] = 'Installation termin�e';
$lang['step2_pwd'] = 'Mot de passe';
$lang['step2_pwd_info'] = 'Il doit rester confidentiel, il permet d\'acc�der au panneau d\'administration.';
$lang['step2_pwd_conf'] = 'Mot de passe [ Confirmer ]';
$lang['step2_pwd_conf_info'] = 'V�rification';
$lang['step1_err_copy'] = 'Copiez le texte en bleu entre les tirets et collez-le dans le fichier mysql.inc.php qui se trouve dans le r�pertoire "include" � la base de l\'endroit o� vous avez install� PhpWebGallery (le fichier mysql.inc.php ne doit comporter QUE ce qui est en bleu entre les tirets, aucun retour � la ligne ou espace n\'est autoris�)';
$lang['install_help'] = 'Besoin d\'aide ? Posez votre question sur le <a href="http://forum.phpwebgallery.net">forum de PhpWebGallery</a>.';
$lang['install_end_message'] = 'La configuration de l\'application s\'est correctement d�roul�e, place � la prochaine �tape<br /><br />
Par mesure de s�curit�, merci de supprimer le fichier "install.php"<br />
Un fois ce fichier supprim�, veuillez suivre ces indications :
<ul>
<li>allez sur la page d\'identification : [ <a href="./identification.php">identification</a> ] et connectez-vous avec le pseudo donn� pour le webmaster</li>
<li>celui-ci vous permet d\'acc�der � la partie administration et aux instructions pour placer les images dans les r�pertoires.</li>
</ul>';
?>