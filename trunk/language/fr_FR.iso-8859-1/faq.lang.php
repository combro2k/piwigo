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

// Admin FAQ
$lang['help_images_title'] = 'Ajouts d\'�l�ments';
$lang['help_images'] =
array(
  'Les r�pertoires repr�sentant les cat�gories sont dans le r�pertoire
"galleries". Ci-dessous l\'arbre des r�pertoires d\'une tr�s petite galerie
(mais utilisant de nombreuses fonctionnalit�s) : <br />
<pre>
.
|-- admin
|-- doc
|-- galleries
|   |-- categorie-1
|   |   |-- categorie-1.1
|   |   |   |-- categorie-1.1.1
|   |   |   |   |-- categorie-1.1.1.1
|   |   |   |   |   |-- pwg_high
|   |   |   |   |   |   +-- mariage.jpg
|   |   |   |   |   |-- thumbnail
|   |   |   |   |   |   +-- TN-mariage.jpg
|   |   |   |   |   +-- mariage.jpg
|   |   |   |   +-- categorie-1.1.1.2
|   |   |   +-- categorie-1.1.2
|   |   |-- categorie-1.2
|   |   |   |-- pookie.jpg
|   |   |   +-- thumbnail
|   |   |       +-- TN-pookie.jpg
|   |   +-- categorie-1.3
|   +-- categorie-2
|       |-- porcinet.gif
|       |-- pwg_representative
|       |   +-- video.avi
|       |-- thumbnail
|       |   +-- TN-porcinet.jpg
|       +-- video.avi
|-- include
|-- install
|-- language
|-- template
+-- tool
</pre>',

  'Fondamentalement, une cat�gorie est repr�sent�e par un r�pertoire �
n\'importe quel niveau sous le r�pertoire "galleries" de votre installation de
PhpWebGallery. Chaque cat�gorie peut contenir autant de sous-niveaux que
d�sir�. Dans l\'exemple ci-dessus, categorie-1.1.1.1 est � un niveau 4 de
profondeur.',

  'Fondamentalement, un �l�ment est repr�sent� par un fichier. Un fichier peut
�tre un �l�ment pour PhpWebGallery si l\'extension du nom du fichier est parmi
la liste $conf[\'file_ext\'] (voir fichier include/config.inc.php). Un fichier
peut �tre une image si son extension est parmi $conf[\'picture_ext\'] (voir
fichier include/config.inc.php).',

  'Les �l�ments de type image doivent avoir une miniature associ�e (voir la
section suivante � propos des miniatures).',

  'Les �l�ments de type image peuvent avoir un image en grand format associ�.
Comme pour le fichier mariage.jpg dans l\'exemple ci-dessus. Aucun pr�fix
n\'est n�cessaire sur le nom du fichier.',

  'Les �l�ments non image (vid�os, sons, fichiers texte, tout ce que vous
voulez...) sont par d�faut repr�sent�s par un ic�ne correspondant �
l\'extension du nom du fichier. Optionnellement, une miniature et un
repr�sentant peuvent �tre associ�s (voir le fichier video.avi dans
l\'exemple)',

  'Attention : le nom d\'un r�pertoire ou d\'un fichier ne doit �tre compos�
que de lettres, de chiffres, de "-", "_" ou ".". Pas d\'espace ou de
caract�res accentu�s.',

  'Conseil : une cat�gorie peut contenir des �l�ments et des sous-cat�gories �
la fois. N�anmoins, il est fortement conseill� pour chaque cat�gorie de choisir
entre contenir des �l�ments OU BIEN des sous-cat�gories.',
  );

$lang['help_thumbnails_title'] = 'Miniatures';
$lang['help_thumbnails'] =
array(
  'Comme mentionn� pr�c�demment, chaque �l�ment de type image doit �tre
associ� � une miniature.',

  'Les miniatures sont stock�es dans le sous-r�pertoire "thumbnail" de chaque
r�pertoire repr�sentant une cat�gorie. Une miniature est un fichier de type
image (m�me extension du nom du fichier) dont le nom de fichier est pr�fix� par
le param�tre "Pr�fixe miniature" (voir zone administration, Configuration,
G�n�ral)',

  'Les miniatures n\'ont pas besoin d\'avoir la m�me extension que leur image
associ�e (une image avec ".jpg" comme extension peut avoir une miniature en
".GIF" par exemple).',

  'Il est conseill� d\'utiliser un outil externe pour la cr�ation des
miniatures (comme ThumbClic ou PhpMyVignettes, voir le site de pr�sentation
de PhpWebGallery).',

  'Vous pouvez �galement utiliser l\'outil de cr�ation de miniature int�gr� �
PhpWebGallery mais cela est d�conseill� car la qualit� risque d\'�tre d�cevante
et cela utilise inutilement les ressources du serveur (ce qui peut �tre un
grave probl�me sur un serveur mutualis�).',

  'Si vous choisissez d\'utiliser le serveur web pour g�n�rer les miniatures,
vous devez donner les droits en �criture sur tous les r�pertoires repr�sentant
les cat�gories pour tous les utilisateurs (propri�taire, groupe, autre)'
  );

$lang['help_database_title'] =
'Synchroniser le syst�me de fichiers et la base';
$lang['help_database'] =
array(
  'Une fois que les fichiers, miniatures, repr�sentants ont �t� correctement
plac�s dans les r�pertoires, se rendre sur : zone administration, G�n�ral,
Synchroniser',

  'Il existe 2 types de synchronisations : structure et meta-donn�es.
Synchroniser la structure revient � synchroniser votre arbre des r�pertoires
et fichiers avec la repr�sentation de la structure dans la base de donn�es.
Synchroniser les m�ta-donn�es permet de mettre � jour les informations comme
le poids du fichier, les dimensions, les donn�es EXIF ou IPTC.',

  'La premi�re synchronisation � effectuer doit �tre celle sur la structure.',

  'Le processus de synchronisation peut prendre du temps (en fonction de la
charge du serveur et de la quantit� de fichiers � g�rer), il est donc
possible d\'avancer pas � pas : cat�gorie par cat�gorie.'
  
  );

$lang['help_access_title'] = 'Autorisations';
$lang['help_access'] =
array(
  'Vous pouvez interdire l\'acc�s aux cat�gories. Les cat�gories peuvent �tre
publiques ou priv�es. Les autorisations (valables pour les groupes et les
utilisateurs) sont g�rables uniquement pour les cat�gories priv�es.',
  
  'Vous pouvez rendre une cat�gorie priv�e en l\'�ditant (zone administration,
Cat�gories, Gestion, Editer) ou en g�rant les options pour votre arbre complet
des cat�gories (zone administration, Cat�gories, S�curit�)',

  'Une fois que certaines cat�gories sont priv�es, vous pouvez g�rer les
autorisations pour les groupes et les utilisateurs (zone administration,
Autorisations).'
  );

$lang['help_groups_title'] = 'Groupes d\'utilisateurs';
$lang['help_groups'] =
array(

  'PhpWebGallery peut g�rer des groupes d\'utilisateurs. Tr�s pratique pour
g�rer des autorisations communes sur les cat�gories priv�es.',

  'Vous pouvez cr�er des groupes et y ajouter des utilisateurs dans la zone
administration, Identification, Groupes',

  'Un utilisateur peut appartenir � plusieurs groupes. L\'autorisation est
plus forte que l\'interdiction : si l\'utilisateur "pierre" appartient aux
groupes "famille" et "amis", et que seul le groupe "famille" peut visiter la
cat�gorie "No�l 2003", alors "pierre" peut visiter cette cat�gorie.'
  
  );

$lang['help_remote_title'] = 'Sites distant';
$lang['help_remote'] =
array(

  'PhpWebGallery offre la possibilit� d\'utiliser plusieurs sites pour
stocker les fichiers qui composeront votre galerie. Cela peut �tre utile si
votre galerie est install�e sur un espace de stockage limit� et que vous avez
de nombreux fichiers � montrer.',

  '1. �diter le fichier tools/create_listing_file.php en modifiant la section
des param�tres comme $conf[\'prefix_thumbnail\'] ou $conf[\'use_exif\'].',

  '2. placer le fichier "tools/create_listing_file.php" modifi� sur votre
site distant, dans le m�me r�pertoire que les r�pertoires repr�sentant vos
cat�gories (comme le r�pertoire "galleries" de ce site) par FTP. Par exemple,
disons que vous pouvez acc�der �
http://exemple.com/galleries/create_listing_file.php.',

  '3. zone administration, G�n�ral, Sites distant. Demander � cr�er un nouveau
site, par exemple http://exemple.com/galleries',

  '4. un nouveau site distant est enregistr�. 4 actions possibles :

<ol>

  <li>g�n�rer la liste : lance une requ�te distant pour g�n�rer le fichier
  de listing distant</li>

  <li>mettre � jour : lit le fichier distant listing.xml et synchronise avec
  la base de donn�es locale</li>

  <li>nettoyer : supprime le fichier distant de listing</li>

  <li>d�truire : supprime le site (et tous les �l�ments qui y sont associ�s)
  dans la base de donn�es</li>

</ol>',

  'Vous pouvez �galement effectuer ces op�rations manuellement en �ditant le
fichier listing.xml � la main et en le d�pla�ant vers votre r�pertoire
"admin". Se rendre sur zone administration, G�n�ral, Sites distant :
PhpWebGallery d�tecte le fichier et propose de s\'en servir.'
  
  );

$lang['help_upload_title'] = 'Ajout de fichiers par les utilisateurs';
$lang['help_upload'] =
array(
  'Pour permettre aux utilisateurs d\'ajouter des fichiers :',

  '1. autoriser l\'ajout d\'images sur n\'importe quelle cat�gorie (zone
administation, Cat�gories, Gestion, Edit ou zone administration, Cat�gories,
Ajout d\'images)',

  '2. donner les droits en �criture (pour tous les utilisateurs) sur les
r�pertoires correspondant aux cat�gories qui sont autoris�es � l\'ajout',

  'Les fichiers ajout�s par les utilisateurs ne sont pas directement visibles
sur le site, ils doivent �tre valid�s par un administrateur. Pour cela, un
administrateur doit se rendre dans zone administration, Images, En attente
afin de valider ou rejeter les fichiers propos�s. Il est ensuite n�cessaire
de synchroniser le syst�me de fichier avec la base de donn�es.'
  );

$lang['help_virtual_title'] = 'Liens entre les �l�ments et les cat�gories, cat�gories virtuelles';
$lang['help_virtual'] =
array(
  'PhpWebGallery dissocie les cat�gories qui stockent les �l�ments et les
cat�gories o� les �l�ments sont montr�s.',

  'Par d�faut, les �lement ne sont montr�s que dans leurs cat�gories r�elles :
celles qui correspondent � leurs r�pertoires sur le serveur.',

  'Pour lier un �l�ment � une cat�gorie, il suffit de faire une association sur
la page d\'�dition de l\'�l�ment (un lien existe vers cette page lorsque
vous �tes connect� en tant qu\'administrateur) ou sur la page regroupant les
informations sur tous les �l�ments d\'une cat�gorie.',

  'En partant de ce principe, il est possible de cr�er des cat�gories
virtuelles : aucun r�pertoire ne correspond � ces cat�gories. Vous pouvez
cr�er des cat�gories virtuelle sur zone administration, Cat�gorie, Gestion.'
  );

$lang['help_infos_title'] = 'Informations diverses';
$lang['help_infos'] =
array(
  'D�s que vous aurez cr�er votre galerie, configurez l\'affichage par d�faut
tel que d�sir� dans zone administation, Configuration, Affichage par
d�faut. En effet, chaque nouvel utilisateur h�ritera de ces propri�t�s
d\'affichage.',

  'Pour tout question, n\'h�sitez pas � visiter le forum ou � y poser une
question si votre recherche est infructueuse. Le <a
href="http://forum.phpwebgallery.net"
style="text-decoration:underline">forum</a> est disponible sur le site de
PhpWebGallery. Consulter �galement la <a href="http://doc.phpwebgallery.net"
style="text-decoration:underline">documentation officielle de
PhpWebGallery</a> pour obtenir plus de d�tails.'
  );
?>