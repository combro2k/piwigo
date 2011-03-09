{footer_script}{literal}
jQuery().ready(function(){
  jQuery("#pLoaderPage  img").fadeTo("fast", 0.6);

  jQuery("#pLoaderPage  img").hover(
    function(){
      jQuery(this).fadeTo("fast", 1.0); // Opacity on hover
    },
    function(){
      jQuery(this).fadeTo("fast", 0.6); // Opacity on mouseout
    }
  );
});
{/literal}{/footer_script}

{html_head}{literal}
<style type="text/css">
#pLoaderPage {
  width:600px;
  margin:0 auto;
  font-size:1.1em;
}

#pLoaderPage P {
  text-align:left;
}

#pLoaderPage .downloads {
  margin:10px auto 0 auto;
}

#pLoaderPage .downloads A {
  display:block;
  width:150px;
  text-align:center;
  font-size:16px;
  font-weight:bold;
}

#pLoaderPage .downloads A:hover {
  border:none;
}

#pLoaderPage LI {
  margin:20px;
}
</style>
{/literal}{/html_head}

<div class="titrePage">
  <h2>{'Piwigo Uploader'|@translate}</h2>
</div>

<div id="pLoaderPage">
<p>{'pLoader stands for <em>Piwigo Uploader</em>. From your computer, pLoader prepares your photos and transfer them to your Piwigo photo gallery.'|@translate}</p>

<ol>
  <li>
    {'Download,'|@translate}

<table class="downloads">
  <tr>
    <td>
      <a href="{$URL_DOWNLOAD_WINDOWS}">
        <img src="http://piwigo.org/screenshots/windows.png" alt="windows">
        <br>Windows
      </a>
    <td>
    <td>
      <a href="{$URL_DOWNLOAD_MAC}">
        <img src="http://piwigo.org/screenshots/mac.png" alt="mac">
        <br>Mac
      </a>
    <td>
    <td>
      <a href="{$URL_DOWNLOAD_LINUX}">
        <img src="http://piwigo.org/screenshots/linux.png" alt="linux">
        <br>Linux
      </a>
    <td>
  </tr>
</table>

  </li>
  <li>{'Install on your computer,'|@translate}</li>
  <li>{'Start pLoader and add your photos.'|@translate}</li>
</ol>
</div>
