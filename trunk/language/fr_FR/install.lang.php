<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
$lang['Initial_config'] = 'Configuration de Base';
$lang['Default_lang'] = 'Langue par défaut de la galerie';
$lang['step1_title'] = 'Configuration de la Base de données';
$lang['step2_title'] = 'Configuration du compte Administrateur';
$lang['Start_Install'] = 'Démarrer l\'installation';
$lang['reg_err_mail_address'] = 'L\'adresse mail doit être de la forme xxx@yyy.eee (exemple : jack@altern.org)';

$lang['install_webmaster'] = 'Administrateur';
$lang['install_webmaster_info'] = 'Cet identifiant apparaîtra à tous vos visiteurs. Il vous sert pour administrer le site';

$lang['step1_confirmation'] = 'Les paramètres rentrés sont corrects';
$lang['step1_err_db'] = 'La connexion au serveur est OK, mais impossible de se connecter à cette base de données';
$lang['step1_err_server'] = 'Impossible de se connecter au serveur';

$lang['step1_dbengine'] = 'Type de base de données';
$lang['step1_dbengine_info'] = 'La base de données à utiliser pour installer piwigo';
$lang['step1_host'] = 'Hôte';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Utilisateur';
$lang['step1_user_info'] = 'nom d\'utilisateur pour votre hébergeur';
$lang['step1_pass'] = 'Mot de passe';
$lang['step1_pass_info'] = 'celui fourni par votre hébergeur';
$lang['step1_database'] = 'Nom de la base';
$lang['step1_database_info'] = 'celui fourni par votre hébergeur';
$lang['step1_prefix'] = 'Préfixe des noms de table';
$lang['step1_prefix_info'] = 'le nom des tables apparaîtra avec ce préfixe (permet de mieux gérer sa base de données)';
$lang['step2_err_login1'] = 'veuillez rentrer un pseudo pour le webmaster';
$lang['step2_err_login3'] = 'le pseudo du webmaster ne doit pas comporter les caractère " et \'';
$lang['step2_err_pass'] = 'veuillez retaper votre mot de passe';
$lang['install_end_title'] = 'Installation terminée';
$lang['step2_pwd'] = 'Mot de passe';
$lang['step2_pwd_info'] = 'Il doit rester confidentiel, il permet d\'accéder au panneau d\'administration.';
$lang['step2_pwd_conf'] = 'Mot de passe [ Confirmer ]';
$lang['step2_pwd_conf_info'] = 'Vérification';
$lang['step1_err_copy'] = 'Copiez le texte en rose entre les tirets et collez-le dans le fichier config_database.inc.php qui se trouve dans le répertoire "include" à la base de l\'endroit où vous avez installé Piwigo (le fichier config_database.inc.php ne doit comporter QUE ce qui est en rose entre les tirets, aucun retour à la ligne ou espace n\'est autorisé)';
$lang['install_help'] = 'Besoin d\'aide ? Posez votre question sur le <a href="%s">forum de Piwigo</a>.';
$lang['install_end_message'] = 'La configuration de l\'application s\'est correctement déroulée, place à la prochaine étape<br><br>
* allez sur la page d\'identification et connectez-vous avec le pseudo donné pour le webmaster<br>
* celui-ci vous permet d\'accéder à la partie administration et aux instructions pour placer les images dans les répertoires.';
$lang['conf_mail_webmaster'] = 'Adresse e-mail de l\'Administrateur';
$lang['conf_mail_webmaster_info'] = 'Les visiteurs pourront vous contacter par ce mail';

$lang['PHP 5 is required'] = 'PHP 5 est requis';
$lang['It appears your webhost is currently running PHP %s.'] = 'Apparemment, la version PHP de votre hébergeur est PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo va essayer de passer en PHP 5 en créant ou en modifiant le fichier .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Notez que vous pouvez changer vous-même la configuration PHP et re-lancer Piwigo après.';
$lang['Try to configure PHP 5'] = 'Essayer de configurer PHP 5';
$lang['Sorry!'] = 'Désolé!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo n\'a pas pu configurer PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = 'Vous devez contacter votre hébergeur afin de savoir comment configurer PHP 5.';
$lang['Hope to see you back soon.'] = 'En espérant vous revoir très prochainement...';

$lang['step1_err_copy_2'] = 'La prochaine étape d\'installation est désormais possible';
$lang['step1_err_copy_next'] = 'étape suivante';

?>