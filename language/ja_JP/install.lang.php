<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'インストレーション';
$lang['Basic configuration'] = '基本設定';
$lang['Default gallery language'] = 'ギャラリーのデフォルト言語';
$lang['Database configuration'] = 'データベース設定';
$lang['Admin configuration'] = '管理設定';
$lang['Start Install'] = 'インストールを開始する';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'メールアドレスは、 xxx@yyy.eee のような形式にしてください (例: jack@altern.org)。';

$lang['Webmaster login'] = 'ウェブマスタログイン';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'ウェブマスタは、ビジターに表示されます。ウェブサイト管理に必要です。';

$lang['Parameters are correct'] = 'パラメータに問題はありません。';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'サーバへ接続することができましたが、データベースに接続できません。';
$lang['Can\'t connect to server'] = 'サーバに接続できません。';
$lang['The next step of the installation is now possible'] = 'インストールの次のステップへ進むことができます。';
$lang['next step'] = '次のステップ';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'ハイフンの間のピンクのテキストをコピーして、ファイル「include/mysql.inc.php」の中に貼り付けてください (警告 : mysql.inc.phpには、ピンクのテキストのみ貼り付けてください。改行またはスペースを含まないでください)。';

$lang['Host'] = 'MySQLホスト';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = '例) localhost、sql.multimania.com、toto.freesurf.fr';
$lang['User'] = 'ユーザ';
$lang['user login given by your host provider'] = 'あなたのホストプロバイダから提供されたデータベースユーザ名です。';
$lang['Password'] = 'パスワード';
$lang['user password given by your host provider'] = 'あなたのホストプロバイダから提供されたデータベースパスワードです。';
$lang['Database name'] = 'データベース名';
$lang['also given by your host provider'] = 'こちらも、あなたのホストプロバイダから提供されたデータベース名です。';
$lang['Database table prefix'] = 'データベーステーブル接頭辞';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'データベーステーブルに接頭辞として付けられます (あなたのテーブルを管理しやすくします)。';
$lang['enter a login for webmaster'] = 'ウェブマスタのユーザIDを入力してください。';
$lang['webmaster login can\'t contain characters \' or "'] = 'ウェブマスタのユーザIDには、「\'」または「"」を含まないでください。';
$lang['please enter your password again'] = 'もう一度あなたのパスワードを入力してください。';
$lang['Installation finished'] = 'インストールが終了しました。';
$lang['Webmaster password'] = 'ウェブマスタパスワード';
$lang['Keep it confidential, it enables you to access administration panel'] = 'ウェブマスタパスワードは、内密にしてください。ウェブマスタパスワードを使用して、あなたは管理パネルにアクセスすることができます。';
$lang['Password [confirm]'] = 'パスワード [もう一度]';
$lang['verification'] = '確認';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'ヘルプが必要ですか? <a href="%s">Piwigoメッセージボード</a>にて、あなたの質問を投稿してください。';
$lang['Webmaster mail address'] = 'ウェブマスタメールアドレス';
$lang['Visitors will be able to contact site administrator with this mail'] = 'ビジターは、このメールアドレスでサイト管理者に連絡することができます。';
$lang['Database type'] = 'データベース種';
$lang['The type of database your piwigo data will be store in'] = 'あなたのPiwigoデータが保存されているデータベース種';
$lang['The configuration of Piwigo is finished, here is the next step<br><br>
* go to the identification page and use the login/password given for webmaster<br>
* this login will enable you to access to the administration panel and to the instructions in order to place pictures in your directories'] = 'Piwigoの設定は完了しました。これは次のステップです。 <br><br>
*アイデンティフィケーション・ページへ進み、ウェブマスター用のユーザー名とパスワードを使ってください。<br>
* このログインはアドミニストレーション・パネルや写真をディレクトリーに追加するための説明書にアクセスできるようになります。';
$lang['PHP 5 is required'] = 'PHP 5は必要';
$lang['It appears your webhost is currently running PHP %s.'] = 'あなたのウェブホストは現在PHP %sを使っているらしいです。';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigoは.htaccess ファイルを作成するか、変更しようとしてPHP 5に設定してみます。';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = '注：自分で設定を変更し、その後Piwigoを再起動もできます。';
$lang['Try to configure PHP 5'] = 'PHP 5を設定してみます。';
$lang['Sorry!'] = '申し訳ありません!';
$lang['Piwigo was not able to configure PHP 5.'] = 'PiwigoはPHP 5に設定できませんでした。';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'あなたのホームページ・プロバイダーのサポートに参照し、自分でPHP 5に変更方法を見られます。';
$lang['Hope to see you back soon.'] = 'またお越し下さい';
?>