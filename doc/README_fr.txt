=============
PhpWebGallery
=============

http://phpwebgallery.net

Installation
============

1. d�compresser � l'aide de winzip par exemple (winrar, winace et beaucoup
   d'autres le permettent �galement) le fichier t�l�charg�.

2. placer les fichiers d�compress�s sur votre serveur web dans le r�pertoire
   de votre choix ("galerie" par exemple)

3. se rendre � l'URL http://votre.domaine/galerie/install.php et suivre les
   instructions

Mise � jour
===========

1. �l�ments � sauvegarder :

 - fichier "include/mysql.inc.php"
 - fichier "include/config_local.inc.php"
 - fichier "template-common/local-layout.css"
 - fichier "template/yoga/local-layout.css"
 - r�pertoire "galleries"
 - �ventuellement th�mes suppl�mentaires et extensions
 - votre base de donn�es (en cr�ant un dump, avec PhpMyAdmin par exemple)

2. supprimer tous les fichiers et r�pertoires de la pr�c�dente installation
   (sauf les �l�ments list�s ci-dessus)

3. d�compresser � l'aide de winzip par exemple (winrar, winace et beaucoup
   d'autres le permettent �galement) le fichier t�l�charg�.

4. placer tous les fichiers de la nouvelle version sur votre site web sauf
   pour les �lements list�s ci-dessus. Les seuls �l�ments venant de la
   pr�c�dente installation sont ceux list�s ci-dessus.

5. se rendre � l'URL http://votre.domaine/galerie/upgrade.php et suivre les
   instructions

Comment commencer
=================

Une fois install�e ou mise � jour, votre galerie est pr�te �
fonctionner. Commencez par vous rendre sur le r�pertoire d'installation dans
votre navigateur : 

http://votre.domaine/galerie

Ensuite, identifiez-vous en tant qu'un administrateur. Un nouveau lien dans
le menu d'identification de la page principale va appara�tre :
Administration. Suivre ce lien :-)

Dans la zone d'administration, prenez tout le temps n�cessaire pour
consulter les instructions, expliquant comment utiliser votre galerie.

Communication
=============

Newsletter
----------

https://gna.org/mail/?group=phpwebgallery

Il est *fortement* recommand� de souscrire � la newsletter de
PhpWebGallery. Tr�s peu de mails sont envoy�s, mais les informations sont
importantes : nouvelles versions de l'application, notification de bugs
importants (relatifs � la s�curit�). Vous trouverez les listes de
discussions disponibles sur la page suivante :

Pas de spam, pas d'utilisation commerciale.

Freshmeat
---------

http://freshmeat.net/projects/phpwebgallery

Permet d'�tre au courant des sorties de toutes les releases, et en
exclusivit� les builds de la branche de d�veloppement (ce qui n'est pas
pr�vu sur les mailing lists "announce").

Outil de suivi de bogues
------------------------

http://bugs.phpwebgallery.net

Gestion des bugs, mais aussi demande de nouvelles fonctionnalit�s. Rien de
plus efficace pour qu'un bug soit corrig� : tant qu'il ne l'est pas, la
"fiche" reste l� � attendre, on ne l'oublie pas comme un topic sur le
forum.

Les demandes d'�volutions sont �galement g�r�es dans cet outil. Ce n'est pas
forc�ment id�al car il ne s'agit pas de la m�me chose, mais le suivi du dev
d'une nouvelle fonctionnalit� peut se mod�liser de la m�me fa�on que le
suivi de la correction d'un bug.

Wiki
----

http://phpwebgallery.net/doc

Documentation suivant le syst�me du wiki. Chacun peut participer �
l'am�lioration de la doc.

Forum de discussion
-------------------

http://forum.phpwebgallery.net

Un forum est disponible et recommand� pour toutes les questions autres que
les demandes d'�volution et rapport de bogue (installation, discussions
techniques).
