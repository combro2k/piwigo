<?php
// +-----------------------------------------------------------------------+
// |                           fr_FR/faq.lang.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                                 |
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

// Admin FAQ
$lang['help_images_title'] = 'Ajout d\'images';
$lang['help_images_intro'] = 'Voici comment il faut placer les images dans vos r�pertoires';
$lang['help_images'][0] = 'dans le r�pertoire "galleries" placer des repertoires qui vont repr�senter vos futures cat�gories';
$lang['help_images'][1] = 'dans chacun de ces r�pertoires, vous avez le droit de cr�er autant de niveau de sous-r�pertoire que vous le souhaitez';
$lang['help_images'][2] = 'vous avez le droit � un nombre illimit� de cat�gories et de sous cat�gories pour chaque cat�gorie';	
$lang['help_images'][3] = 'les fichiers images doivent �tre au format jpg (extension jpg ou JPG), gif (GIF ou gif) ou encore png (PNG ou png)';
$lang['help_images'][4] = 'Evitez d\'utiliser des espaces " " ou des tirets "-" dans les noms de fichiers ou de cat�gorie, je conseille d\'utiliser le caract�re underscore "_" qui est g�r� par l\'application et donnera des r�sultats plus appr�ciables';
$lang['help_thumbnails_title'] = 'Miniatures';
$lang['help_thumbnails'][0] = 'dans chaque r�pertoire contenant des images � afficher sur le site, il y a un sous-r�pertoire nomm� "thumbnail", s\'il n\'existe pas, cr�ez-le pour placer vos miniatures dedans';
$lang['help_thumbnails'][1] = 'les miniatures n\'ont pas besoin d\'avoir la m�me extension que les images associ�es (une image en .jpg peut avoir sa miniature en .GIF par exemple)';
$lang['help_thumbnails'][2] = 'la miniature associ�e � une image doit �tre pr�fix�e par le pr�fixe donn� sur la page de configuration g�n�rale (image.jpg -> TN_image.GIF par exemple).';
$lang['help_thumbnails'][3] = 'il est conseill� d\'utiliser le module pour windows t�l�chargeable sur le site de PhpWebGallery pour la cr�ation des miniatures.';
$lang['help_thumbnails'][4] = 'vous pouvez utilisez la gestion de cr�ation de miniatures, int�gr�e � PhpWebGallery, mais ce n\'est pas conseill�, car la qualit� des miniatures sera moindre qu\'avec un v�ritable outil de manipulation d\'images et que cela consommera des ressources sur le serveur, ce qui peut se r�v�ler g�nant pour un h�bergement gratuit.';
$lang['help_thumbnails'][5] = 'si vous choisissez d\'utiliser votre h�bergeur pour cr�er les miniatures, il faut avant cela passer le r�pertoire "galleries" en 775 ainsi que tous ses sous-r�pertoires.';
$lang['help_database_title'] = 'Remplissage de la base de donn�es';
$lang['help_database'][0] = 'Une fois les fichiers plac�s correctement et les miniatures plac�es ou cr��es, cliquez sur "MaJ base d\'images" dans le menu de la zone d\'administration.';
$lang['help_remote_title'] = 'Site distant';
$lang['help_remote'][0] = 'PhpWebGallery offre la possibilit� d\'utiliser plusieurs serveurs pour stocker les images qui composeront votre galerie. Cela peut �tre utile si votre galerie est install�e sur une espace limit� et que vous avez une grande quantit� d\'images � montrer. Suivez la proc�dure suivante :';
$lang['help_remote'][1] = '1. �ditez le fichier "create_listing_file.php" (vous le trouverez dans le r�pertoire "admin"), en modifiant la ligne "$prefix_thumbnail = "TN-";" si le pr�fixe pour vos miniatures n\'est pas "TN-".';
$lang['help_remote'][2] = '2. placez le fichier "create_listing_file.php" modifi� sur votre site distant, dans le r�pertoire racine de vos r�pertoires d\'images (comme le r�pertoire "galleries" du pr�sent site) par ftp.';
$lang['help_remote'][3] = '3. lancez le script en allant � l\'url http://domaineDistant/repGalerie/create_listing_file.php, un fichier listing.xml vient de se cr�er.';
$lang['help_remote'][4] = '4. r�cup�rez le fichier listing.xml de votre site distant pour le placer dans le r�pertoire "admin" du pr�sent site.';
$lang['help_remote'][5] = '5. lancez une mise � jour de la base d\'images par l\'interface d\'administration, une fois le fichier listing.xml utilis�, supprimez le du r�pertoire "admin".';
$lang['help_remote'][6] = 'Vous pouvez mettre � jour le contenu d\'un site distant en refaisant la manipulation d�crite. Vous pouvez �galement supprimer un site distant en choisissant l\'option dans la section configuration du panneau d\'administration.';
$lang['help_upload_title'] = 'Ajout d\'images par les utilisateurs';
$lang['help_upload'][0] = 'PhpWebGallery offre la possibilit� aux visiteurs d\'uploader des images. Pour cela :';
$lang['help_upload'][1] = '1. autorisez l\'option dans la zone configuration du panneau d\'administration';
$lang['help_upload'][2] = '2. autorisez les droits en �criture sur les r�pertoires d\'images';
$lang['help_infos_title'] = 'Informations compl�mentaires';
$lang['help_infos'][1] = 'D�s que vous avez cr�� votre galerie, allez dans la gestion des utilisateurs et modifiez les permissions pour l\'utilisateur visiteur. En effet, tous les utilisateurs qui s\'enregistrent eux-m�me auront par d�faut les m�mes permissions que l\'utilisateur "visiteur".';
$lang['help_database'][1] = 'Afin d\'�viter la mise � jour d\'un trop grand nombre d\'images, commencez par mettre � jour uniquement les cat�gories, puis sur la page des cat�gories, mettre � jour chaque cat�gorie individuellement gr�ce au lien "mise � jour"';
$lang['help_upload'][3] = 'La cat�gorie doit elle-m�me �tre autoris�e pour l\'upload.';
$lang['help_upload'][4] = 'Les images upload�es par les visiteurs ne sont pas directement visibles sur le site, elles doivent �tre valid�es par un administrateur. Pour cela, un administrateur doit se rendre sur la page "en attente" du panneau d\'administration, valider ou refuser les images propos�e, puis lancer une mise � jour de la base d\'images.';
$lang['help_virtual_title'] = 'Liens images vers cat�gories et cat�gories virtuelles';
$lang['help_virtual'][0] = 'PhpWebGallery permet de dissocier les cat�gories o� sont stock�es les images et les cat�gories o� les images apparaissent.';
$lang['help_virtual'][1] = 'Par d�faut, les images apparaissent uniquement dans leurs cat�gories r�elles : celles qui correspondent � des r�pertoires sur le serveur web.';
$lang['help_virtual'][2] = 'Pour lier une image � une cat�gorie, il suffit de l\'y associer via la page de modification d\'une image ou par lot sur la page de modification des images d\'une cat�gorie.';
$lang['help_virtual'][3] = 'En partant de ce principe, il est possible de cr�er des cat�gories virtuelles dans PhpWebGallery : aucun r�pertoire "r�el" n\'y est rattach� sur le disque du serveur. Il suffit simplement de cr�er la cat�gorie sur la page de la liste des cat�gories existantes dans la zone d\'administration.';
$lang['help_groups_title'] = 'Groupes d\'utilisateurs';
$lang['help_groups'][0] = 'PhpWebGallery permet de g�rer des groupes d\'utilisateurs, cela est tr�s utile pour regrouper les autorisations d\'acc�s aux cat�gories priv�es.';
$lang['help_groups'][1] = '1. Cr�ez un groupe "famille" sur la page des groupes de la zone d\'administration.';
$lang['help_groups'][2] = '2. Sur la page de la liste des utilisateurs, en �diter un, et l\'associer au groupe "famille".';
$lang['help_groups'][3] = '3. En modifiant les permissions pour une cat�gorie, ou pour un groupe, vous verrez que toutes les cat�gories autoris�es � un groupe le sont pour les membres de ce groupe.';
$lang['help_groups'][4] = 'Un utilisateurs peut appartenir � plusieurs groupes. L\'autorisation est plus forte que l\'interdiction : si l\'utilisateur "paul" appartient au groupe "famille" et "amis", et que seule le groupe "famille" est autoris�e � consulter la cat�gorie priv�e "No�l 2003", alors "paul" y aura acc�s.';
$lang['help_access_title'] = 'Autorisations d\'acc�s';
$lang['help_access'][0] = 'PhpWebGallery dispose d\'un syst�me de restrictions d\'acc�s aux cat�gories souhait�es. Les cat�gories sont soit publiques, soit priv�es. Pour interdire l\'acc�s par d�faut � une cat�gorie :';
$lang['help_access'][1] = '1. Editez la cat�gorie (depuis la page des cat�gories dans la zone d\'administration) et rendez la "priv�e".';
$lang['help_access'][2] = '2. Sur les pages des permissions (d\'un groupe, d\'utilisateur) la cat�gorie appara�tra et vous pourrez autoriser l\'acc�s ou non.';
$lang['help_infos'][2] = 'Pour n\'importe quelle question, n\'h�sitez pas � consulter le <a href="'.PHPWG_FORUM_URL.'" style="text-decoration:underline">forum</a> ou � y poser une question, sur le site';
?>