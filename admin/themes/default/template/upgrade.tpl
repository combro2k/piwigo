<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html lang="{$lang_info.code}" dir="{$lang_info.direction}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-script-type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="shortcut icon" type="image/x-icon" href="{$ROOT_URL}{$themeconf.icon_dir}/favicon.ico">

{foreach from=$themes item=theme}
{if isset($theme.local_head)}{include file=$theme.local_head}{/if}
<link rel="stylesheet" type="text/css" href="{$ROOT_URL}admin/themes/{$theme.id}/theme.css">
{/foreach}

{literal}
<style type="text/css">
body {
  background:url("admin/themes/roma/images/bottom-left-bg.jpg") no-repeat fixed left bottom #111111;
}

.content {
  background:url("admin/themes/roma/images/fillet.png") repeat-x scroll left top #222222;
  width: 800px;
  min-height: 0px !important;
  margin: auto;
  text-align: left;
  padding: 5px;
}

#headbranch  {
  background:url("admin/themes/roma/images/top-left-bg.jpg") no-repeat scroll left top transparent;
}

#theHeader {
  display: block;
  background:url("admin/themes/roma/images/piwigo_logo_sombre_214x100.png") no-repeat scroll 245px top transparent;
}

.content h2 {
  display:block;
  font-size:28px;
  height:104px;
  width:54%;
  color:#666666;
  letter-spacing:-1px;
  margin:0 30px 3px 20px;
  overflow:hidden;
  position:absolute;
  right:0;
  text-align:right;
  top:0;
  width:770px;
  text-align:right;
  text-transform:none; 
}

table { margin: 0px; }
td {  padding: 3px 10px; }
textarea { margin-left: 20px; }
</style>
{/literal}
<title>Piwigo {$RELEASE} - {'Upgrade'|@translate}</title>
</head>

<body>
<div id="headbranch"></div> {* Dummy block for double background management *}
<div id="the_page">
<div id="theHeader"></div>
<div id="content" class="content">

{if isset($introduction)}
<h2>Piwigo {$RELEASE} - {'Upgrade'|@translate}</h2>

{if isset($errors)}
<div class="errors">
  <ul>
    {foreach from=$errors item=error}
    <li>{$error}</li>
    {/foreach}
  </ul>
</div>
{/if}

<table>
  <tr>
    <td>{'Language'|@translate}</td>
    <td>
      <select name="language" onchange="document.location = 'upgrade.php?language='+this.options[this.selectedIndex].value;">
        {html_options options=$language_options selected=$language_selection}
      </select>
    </td>
  </tr>
</table>

<p>{'introduction message'|@translate|@sprintf:$introduction.CURRENT_RELEASE}</p>
{if isset($login)}
<p>{'Only administrator can run upgrade: please sign in below.'|@translate}</p>
{/if}

<form method="POST" action="{$introduction.F_ACTION}" name="upgrade_form">
{if isset($login)}
<table>
  <tr>
    <td>{'Username'|@translate}</td>
    <td><input type="text" name="username" id="username" size="25" maxlength="40" style="width: 150px;"></td>
  </tr>
  <tr>
    <td>{'Password'|@translate}</td>
    <td><input type="password" name="password" id="password" size="25" maxlength="25" style="width: 150px;"></td>
  </tr>
</table>
{/if}

<p style="text-align: center;">
<input class="submit" type="submit" name="submit" value="{'Upgrade from %s to %s'|@translate|@sprintf:$introduction.CURRENT_RELEASE:$RELEASE}">
</p>
</form>
<!--
<p style="text-align: center;">
<a href="{$introduction.RUN_UPGRADE_URL}">{'Upgrade from %s to %s'|@translate|@sprintf:$introduction.CURRENT_RELEASE:$RELEASE}</a>
</p>
-->

{/if}

{if isset($upgrade)}
<h2>{'Upgrade from %s to %s'|@translate|@sprintf:$upgrade.VERSION:$RELEASE}</h2>

<p><b>{'Statistics'|@translate}</b></p>
<ul>
  <li>{'total upgrade time'|@translate} : {$upgrade.TOTAL_TIME}</li>
  <li>{'total SQL time'|@translate} : {$upgrade.SQL_TIME}</li>
  <li>{'SQL queries'|@translate} : {$upgrade.NB_QUERIES}</li>
</ul>

<p><b>{'Upgrade informations'|@translate}</b></p>
<ul>
  {foreach from=$infos item=info}
  <li>{$info}</li>
  {/foreach}
</ul>

<form action="index.php" method="post">
<p><input type="submit" name="submit" value="{'Home'|@translate}"></p>
</form>
{/if}

</div> {* content *}
<div>{$L_UPGRADE_HELP}</div>
</div> {* the_page *}
</body>
</html>
