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

// Main words
$lang['links'] = 'Liens';
$lang['general'] = 'G�n�ral';
$lang['config'] = 'Configuration';
$lang['users'] = 'Utilisateurs';
$lang['instructions'] = 'Instructions';
$lang['history'] = 'Historique';
$lang['manage'] = 'Gestion';
$lang['waiting'] = 'En attente';
$lang['access'] = 'Acc�s';
$lang['groups'] = 'Groupes';
$lang['permissions'] = 'Autorisations';
$lang['update'] = 'Synchroniser';
$lang['edit'] = 'Editer';
$lang['authorized'] = 'Autoris�';
$lang['forbidden'] = 'Interdit';
$lang['public'] = 'Publique';
$lang['private'] = 'Priv�e';
$lang['metadata']='M�tadonn�es';
$lang['visitors'] = 'Visiteurs';
$lang['storage'] = 'R�pertoire';
$lang['locked'] = 'Verrouill�e';
$lang['unlocked'] = 'D�verrouill�e';
$lang['lock'] = 'Verrouiller';
$lang['unlock'] = 'D�verrouiller';
$lang['up'] = 'Monter';
$lang['down'] = 'Descendre';
$lang['path'] = 'Chemin d\'acc�s';

// Specific words
$lang['phpinfos'] = 'Informations PHP';
$lang['remote_site'] = 'Site distant';
$lang['remote_sites'] = 'Sites distant';
$lang['gallery_default'] = 'Options par d�faut';
$lang['upload'] = 'Ajout d\'images';

// Remote sites management
$lang['remote_site_create'] = 'Cr�er un nouveau site distant : (give its URL to generate_file_listing.php)';
$lang['remote_site_uncorrect_url'] = 'Remote site url must start by http or https and must only contain characters among "/", "a-zA-Z0-9", "-" or "_"';
$lang['remote_site_already_exists'] = 'Ce site existe d�j�';
$lang['remote_site_generate'] = 'g�n�rer la liste';
$lang['remote_site_generate_hint'] = 'generate file listing.xml on remote site';
$lang['remote_site_update'] = 'update';
$lang['remote_site_update_hint'] = 'read remote listing.xml and updates database';
$lang['remote_site_clean'] = 'clean';
$lang['remote_site_clean_hint'] = 'remove remote listing.xml file';
$lang['remote_site_delete'] = 'delete';
$lang['remote_site_delete_hint'] = 'delete this site and all its attached elements';
$lang['remote_site_file_not_found'] = 'file create_listing_file.php on remote site was not found';
$lang['remote_site_error'] = 'an error happened';
$lang['remote_site_listing_not_found'] = 'remote listing file was not found';
$lang['remote_site_removed'] = 'was removed on remote site';
$lang['remote_site_removed_title'] = 'Removed elements';
$lang['remote_site_created'] = 'created';
$lang['remote_site_deleted'] = 'deleted';
$lang['remote_site_local_found'] = 'A local listing.xml file has been found for ';
$lang['remote_site_local_new'] = '(new site)';
$lang['remote_site_local_update'] = 'read local listing.xml and update';

// Category words
$lang['cat_security'] = 'S�curit�';
$lang['cat_options'] = 'Options de la cat�gorie';
$lang['cat_add'] = 'Ajouter une cat�gorie virtuelle';
$lang['cat_virtual'] = 'Cat�gorie virtuelle';
$lang['cat_public'] = 'Cat�gorie publique';
$lang['cat_private'] = 'Cat�gorie priv�e';
$lang['cat_image_info'] = 'Infos images';
$lang['editcat_status'] = 'Statut';
$lang['editcat_confirm'] = 'Les informations associ�es � cette cat�gorie ont �t� mises � jour.';
$lang['editcat_perm'] = 'Pour acc�der aux permissions associ�es, cliquez';
$lang['editcat_lock_info'] = 'Verrouiller temporairement une cat�gorie (maintenance). Elle devient alors invisible pour les utilisateurs.';
$lang['editcat_uploadable'] = 'Autoriser l\'ajout d\'images';
$lang['editcat_uploadable_info'] = 'Les utilisateurs pourront ajouter des images.';
$lang['editcat_commentable_info'] = 'Autoriser les utilisateurs � poster des commentaires.';
$lang['cat_access_info'] = 'Permet de g�rer l\'acc�s � cette cat�gorie.';
$lang['cat_virtual_added'] = 'Cat�gorie virtuelle cr��e';
$lang['cat_virtual_deleted'] = 'Cat�gorie virtuelle d�truite';
$lang['cat_upload_title'] = 'S�lectionner les cat�gories pour lesquelles l\'ajout d\'image est autoris�';
$lang['cat_upload_info'] = 'Seules les cat�gories non virtuelles et non distantes sont repertori�es.';
$lang['cat_lock_title'] = 'Verrouiller les cat�gories';
$lang['cat_lock_info'] = 'Verrouiller temporairement une cat�gorie (maintenance). Elle devient alors invisible pour les utilisateurs.
<br />Toutes les sous-cat�gories seront aussi verrouill�es ou toutes les cat�gories m�res seront d�verouill�es selon votre action.';
$lang['cat_comments_title'] = 'Autoriser les utilisateurs � poster des commentaires';
$lang['cat_comments_info'] = 'Par h�ritage, il est possible de poster des commentaires dans une sous-cat�gorie si cela est autoris� pour au moins une cat�gorie m�re.';
$lang['cat_status_title'] = 'Gestion des autorisations';
$lang['cat_status_info'] = 'Les cat�gories s�lectionn�es sont priv�es : vous devrez permettre � vos utilisateurs et / ou groupes d\'y acc�der.
<br />Si une cat�gorie est d�clar�e priv�e, alors toutes ses sous cat�gories deviennent priv�es.
<br />Si une cat�gorie est d�clar�e publique, alors toutes les cat�gories m�res deviennent publiques.';
$lang['cat_representant'] = 'Repr�sentant au hasard';

//Titles
$lang['admin_panel'] = 'Panneau d\'administration';
$lang['default_message'] = 'Zone d\'administration de PhpWebGallery';
$lang['title_liste_users'] = 'Liste des utilisateurs';
$lang['title_history'] = 'Historique';
$lang['title_update'] = 'Mise � jour de la base de donn�es';
$lang['title_configuration'] = 'Configuration de PhpWebGallery';
$lang['title_instructions'] = 'Instructions';
$lang['title_categories'] = 'Gestion des cat�gories';
$lang['title_edit_cat'] = 'Editer une cat�gorie';
$lang['title_info_images'] = 'Modifier les informations sur les images d\'une cat�gorie';
$lang['title_thumbnails'] = 'Cr�ation des miniatures';
$lang['title_thumbnails_2'] = 'pour';
$lang['title_default'] = 'Administration de PhpWebGallery';
$lang['title_waiting'] = 'Images en attente de validation';
$lang['title_upload'] = 'S�lectionner les cat�gories pour lesquelles l\'ajout d\'image est autoris�';
$lang['title_cat_options'] = 'Options relatives aux cat�gories';
$lang['title_groups'] = 'Gestion des groupes';

//Error messages
$lang['conf_confirmation'] = 'Informations enregistr�es dans la base de donn�es';
$lang['cat_error_name'] = 'Le nom d\'une cat�gorie ne doit pas �tre nul';

// Configuration
$lang['conf_default'] = 'Affichage par d�faut';
$lang['conf_cookie'] = 'Session & Cookie';

// Configuration -> general
$lang['conf_general_title'] = 'Configuration g�n�rale';
$lang['conf_mail_webmaster'] = 'Adresse e-mail de l\'Administrateur';
$lang['conf_mail_webmaster_info'] = 'Les visiteurs pourront vous contacter par ce mail';
$lang['conf_mail_webmaster_error'] = 'Adresse email non valide. Elle doit �tre de la forme : nom@domaine.com';
$lang['conf_prefix'] = 'Pr�fixe thumbnail';
$lang['conf_prefix_info'] = 'Les noms des fichiers miniatures en sont pr�fix�. Laissez vide en cas de doute.';
$lang['conf_prefix_thumbnail_error'] = 'Le pr�fixe doit �tre uniquement compos� des caract�res suivant : a-z, "-" ou "_"';
$lang['conf_access'] = 'Type d\'acces';
$lang['conf_log_info'] = 'historiser les visites sur le site ? Les visites seront visibles dans l\'historique de l\'administration';
$lang['conf_notification'] = 'Notification par mail';
$lang['conf_notification_info'] = 'Notification automatique par mail des administrateurs (seuls les administrateurs) lors de l\'ajout d\'un commentaire, ou lors de l\'ajout d\'une image.';

// Configuration -> comments
$lang['conf_comments_title'] = 'Configuration des commentaires';
$lang['conf_comments_forall'] = 'Autoriser pour tous ?';
$lang['conf_comments_forall_info'] = 'M�me les invit�s non enregistr�s peuvent d�poser les messages';
$lang['conf_nb_comment_page'] = 'Nombre de commentaires par page';
$lang['conf_nb_comment_page_info'] = 'Nombre de commentaire � afficher sur chaque page. Le nombre de commentaires pour une image reste illimit�. Entrer un nombre entre 5 et 50.';
$lang['conf_nb_comment_page_error'] = 'Le nombre de commentaires par page doit �tre compris entre 5 et 50 inclus.';
$lang['conf_comments_validation'] = 'Validation';
$lang['conf_comments_validation_info'] = 'L\'administrateur valide les commentaires avant qu\'ils apparaissent sur le site';

// Configuration -> default
$lang['conf_default_title'] = 'Configuration de l\'affichage par d�faut';
$lang['conf_default_language_info'] = 'Langue par d�faut';
$lang['conf_default_theme_info'] = 'Th�me par d�faut';
$lang['conf_nb_image_line_info'] = 'Nombre d\'images par ligne par d�faut';
$lang['conf_nb_line_page_info'] = 'Nombre de lignes par page par d�faut';
$lang['conf_recent_period_info'] = 'En nombre de jours. P�riode pendant laquelle l\'image est not�e comme r�cente. La dur�e doit au moins �tre d\'un jour.';
$lang['conf_default_expand_info'] = 'D�velopper toutes les cat�gories par d�faut dans le menu ?';
$lang['conf_show_nb_comments_info'] = 'Montrer le nombre de commentaires pour chaque image sur la page des miniatures';
$lang['conf_default_maxwidth_info'] = 'Largeur maximum affichable pour les images : les images ne seront redimensionn�es que pour l\'affichage, les fichiers images resteront intacts. 
Laisser vide si vous ne souhaitez pas mettre de limite.';
$lang['conf_default_maxheight_info'] = 'Idem mais pour la hauteur des images';

// Configuration -> upload
$lang['conf_upload_title'] = 'Configuration de l\'envoi d\'images par les utilisateurs';
$lang['conf_upload_maxfilesize'] = 'Poids maximum';
$lang['conf_upload_maxfilesize_info'] = 'Poids maximum autoris� pour les images upload�es. Celui-ci doit �tre un entier compris entre 10 et 1000, en Ko.';
$lang['conf_upload_maxfilesize_error'] = 'Le poids maximum pour les images upload�s doit �tre un entier compris entre 10 et 1000.';
$lang['conf_upload_maxwidth'] = 'Largeur maximum';
$lang['conf_upload_maxwidth_info'] = 'Largeur maximum autoris�e pour les images. Celle-ci doit �tre un entier sup�rieur � 10, en pixels.';
$lang['conf_upload_maxwidth_error'] = 'la largeur maximum des images upload�es doit �tre un entier sup�rieur � 10.';
$lang['conf_upload_maxheight'] = 'Hauteur maximum';
$lang['conf_upload_maxheight_info'] = 'Hauteur maximum autoris�e pour les images. Celle-ci doit �tre un entier sup�rieur � 10, en pixels.';
$lang['conf_upload_maxheight_error'] = 'La hauteur maximum des images upload�es doit �tre un entier sup�rieur � 10.';
$lang['conf_upload_tn_maxwidth'] = 'Largeur maximum miniatures.';
$lang['conf_upload_tn_maxwidth_info'] = 'Largeur maximum autoris�e pour les miniatures. Celle-ci doit �tre un entier sup�rieur � 10, en pixels.';
$lang['conf_upload_maxwidth_thumbnail_error'] = 'La largeur maximum des miniatures upload�es doit �tre un entier sup�rieur � 10.';
$lang['conf_upload_tn_maxheight'] = 'Hauteur maximum miniatures';
$lang['conf_upload_tn_maxheight_info'] = 'Hauteur maximum autoris�e pour les miniatures. Celle-ci doit �tre un entier sup�rieur � 10, en pixels.';
$lang['conf_upload_maxheight_thumbnail_error'] = 'La hauteur maximum des miniatures upload�es doit �tre un entier sup�rieur � 10.';

// Configuration -> session
$lang['conf_session_title'] = 'Configuration des sessions';
$lang['conf_authorize_remembering'] = 'Connexion automatique';
$lang['conf_authorize_remembering_info'] = 'Les utilisateurs ne devront plus s\'identifier � chaque nouvelle visiste du site';

// Configuration -> metadata
$lang['conf_metadata_title'] = 'Configuration des m�tadonn�es des images';
$lang['conf_use_exif'] = 'Analyse des EXIF';
$lang['conf_use_exif_info'] = 'Analyse les donn�es EXIF durant la synchronisation des images';
$lang['conf_use_iptc'] = 'Analyse des IPTC';
$lang['conf_use_iptc_info'] = 'Analyse les donn�es IPTC durant la synchronisation des images';
$lang['conf_show_exif'] = 'Montrer les EXIF';
$lang['conf_show_exif_info'] = 'Affiche les m�tadonn�es EXIF';
$lang['conf_show_iptc'] = 'Montrer les IPTC';
$lang['conf_show_iptc_info'] = 'Affiche les m�tadonn�es IPTC';

// Image informations
$lang['infoimage_general'] = 'Options g�n�rale pour la cat�gorie';
$lang['infoimage_useforall'] = 'utiliser pour toutes les images ?';
$lang['infoimage_creation_date'] = 'Date de cr�ation';
$lang['infoimage_detailed'] = 'Options pour chaque image / photo';
$lang['infoimage_title'] = 'Titre';
$lang['infoimage_keyword_separation'] = '(s�parer avec des ",")';
$lang['infoimage_addtoall'] = 'ajouter � tous';
$lang['infoimage_removefromall'] = 'retirer � tous';
$lang['infoimage_associate'] = 'Associer � la cat�gorie';
$lang['infoimage_associated'] = 'Associ�';
$lang['infoimage_dissociated'] = 'Non associ�';
$lang['storage_category'] = 'Repertoire de stockage';
$lang['represents'] = 'Repr�sente';
$lang['doesnt_represent'] = 'doesn\'t represent';

// Thumbnails
$lang['tn_width'] = 'largeur';
$lang['tn_height'] = 'hauteur';
$lang['tn_no_support'] = 'Image inexistante ou aucun support';
$lang['tn_format'] = 'pour le format';
$lang['tn_thisformat'] = 'pour ce format de fichier';
$lang['tn_err_width'] = 'la largeur doit �tre un entier sup�rieur �';
$lang['tn_err_height'] = 'la hauteur doit �tre un entier sup�rieur �';
$lang['tn_results_title'] = 'R�sultats de la miniaturisation';
$lang['tn_picture'] = 'image';
$lang['tn_results_gen_time'] = 'g�n�r� en';
$lang['tn_stats'] = 'Statistiques g�n�rales';
$lang['tn_stats_nb'] = 'nombre d\'images miniaturis�es';
$lang['tn_stats_total'] = 'temps total';
$lang['tn_stats_max'] = 'temps max';
$lang['tn_stats_min'] = 'temps min';
$lang['tn_stats_mean'] = 'temps moyen';
$lang['tn_err'] = 'Vous avez commis des erreurs';
$lang['tn_params_title'] = 'Param�tres de miniaturisation';
$lang['tn_params_GD'] = 'version de GD';
$lang['tn_params_GD_info'] = '- GD est la biblioth�que de manipulation graphique pour PHP<br />
- cochez la version de GD install�e sur le serveur. Si vous choisissez l\'une et que vous obtenez ensuite des messages d\'erreur, choisissez l\'autre version. 
Si aucune version ne marche, cela signifie que GD n\'est pas install� sur le serveur.';
$lang['tn_params_width_info'] = 'largeur maximum que peut prendre les miniatures';
$lang['tn_params_height_info'] = 'hauteur maximum que peut prendre les miniatures';
$lang['tn_params_create'] = 'en cr�er';
$lang['tn_params_create_info'] = 'N\'essayez pas de lancer directement un grand nombre de miniaturisation.<br />
En effet la miniaturisation est co�teuse en ressources processeur pour le serveur. 
Si vous �tes chez un h�bergeur gratuit, une trop forte occupation processeur peut amener l\'h�bergeur � supprimer votre compte.';
$lang['tn_params_format'] = 'format';
$lang['tn_params_format_info'] = 'seul le format jpeg est support� pour la cr�ation des miniatures';
$lang['tn_alone_title'] = 'images sans miniatures (format jpg et png uniquement)';
$lang['tn_dirs_title'] = 'Liste des r�pertoires';
$lang['tn_dirs_alone'] = 'images sans miniatures';

// Update
$lang['update_missing_tn'] = 'Il manque la miniature pour';
$lang['update_disappeared_tn'] = 'La miniature n\'existe pas';
$lang['update_disappeared'] = 'n\'existe pas';
$lang['update_part_deletion'] = 'Suppression des images de la base qui n\'ont pas de thumbnail ou qui n\'existent pas';
$lang['update_part_research'] = 'Recherche des nouvelles images dans les r�pertoires';
$lang['update_research_added'] = 'ajout�';
$lang['update_research_tn_ext'] = 'miniature en';
$lang['update_default_title'] = 'Type de mise � jour';
$lang['update_nb_new_elements'] = '�l�ment(s) ajout�(s)';
$lang['update_nb_del_elements'] = '�l�ment(s) effac�(s)';
$lang['update_nb_new_categories'] = 'cat�gorie(s) ajout�e(s)';
$lang['update_nb_del_categories'] = 'cat�gorie(s) effac�e(s)';
$lang['update_sync_files'] = 'Synchroniser la structure';
$lang['update_sync_dirs'] = 'Seulement les cat�gories';
$lang['update_sync_all'] = 'Cat�gories et fichiers';
$lang['update_sync_metadata'] = 'Synchroniser les m�ta-donnn�es';
$lang['update_sync_metadata_new'] = 'Seulement sur les nouveaux �l�ments';
$lang['update_sync_metadata_all'] = 'Sur tous les �l�ments';
$lang['update_cats_subset'] = 'Limiter la synchronisation aux cat�gories suivantes';

// History
$lang['stats_title'] = 'Historique de l\'ann�e �coul�e';
$lang['stats_month_title'] = 'Historique mois par mois';
$lang['stats_pages_seen'] = 'Pages vues';
$lang['stats_empty'] = 'vider l\'historique';
$lang['stats_global_graph_title'] = 'Nombre de pages vues par mois';
$lang['stats_visitors_graph_title'] = 'Nombre de visiteurs par jour';

// Users
$lang['user_err_modify'] = 'Cet utilisateur ne peut pas �tre modif� ou supprim�';
$lang['user_err_unknown'] = 'Cet utilisateur n\'existe pas dans la base de donn�es';
$lang['user_management'] = 'Champs sp�ciaux pour l\'administrateur';
$lang['user_status'] = 'Statut de l\'utilisateur';
$lang['user_status_admin'] = 'Administrateur';
$lang['user_status_guest'] = 'Utilisateur';
$lang['user_delete'] = 'Supprimer l\'utilisateur';
$lang['user_delete_hint'] = 'Cliquez ici pour supprimer d�finitivement l\'utilisateur. Attention cette op�ration ne pourra �tre r�tablie.';

// Groups
$lang['group_list_title'] = 'Liste des groupes existants';
$lang['group_confirm_delete']= 'Confirmer la destruction du groupe';
$lang['group_add'] = 'Ajouter un groupe';
$lang['group_add_error1'] = 'Le nom du groupe ne doit pas comporter de " ou de \' et ne pas �tre vide.';
$lang['group_add_error2'] = 'Ce nom de groupe est d�j� utilis�.';
$lang['group_edit'] = 'Edition des utilisateurs appartenant au groupe';
$lang['group_deny_user'] = 'Supprimer la s�lection';
$lang['group_add_user']= 'Ajouter le membre';


// To be done


$lang['permuser_info_message'] = 'Permissions enregistr�es';
$lang['permuser_title'] = 'Restrictions pour l\'utilisateur';
$lang['permuser_warning'] = 'Attention : un "<span style="font-weight:bold;">acc�s interdit</span>" � la racine d\'une cat�gorie emp�che l\'acc�s � toute la cat�gorie';
$lang['permuser_parent_forbidden'] = 'cat�gorie parente interdite';




$lang['title_add'] = 'Ajouter un utilisateur';
$lang['title_modify'] = 'Modifier un utilisateur';

$lang['title_user_perm'] = 'Modifier les permissions pour l\'utilisateur';
$lang['title_cat_perm'] = 'Modifier les permissions pour la cat�gorie';
$lang['title_group_perm'] = 'Modifier les permissions pour le groupe';
$lang['title_picmod'] = 'Modifier les informations d\'une image';
$lang['waiting_update'] = 'Les images valid�es ne seront visibles qu\'apr�s mise � jour de la base d\'images.';
$lang['permuser_only_private'] = 'Seules les cat�gories priv�es sont repr�sent�es';

$lang['comments_last_title'] = 'Derniers commentaires';
$lang['comments_non_validated_title'] = 'Commentaires non valid�s';
$lang['cat_unknown_id'] = 'Cette cat�gorie n\'existe pas dans la base de donn�es';
$lang['conf_remote_site_delete_info'] = 'Supprimer un site revient � supprimer toutes les images et les cat�gories en relation avec ce site.';
?>