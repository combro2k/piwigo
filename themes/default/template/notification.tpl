{html_head} 
<link rel="alternate" type="application/rss+xml" title="{'Photos only RSS feed'|@translate}" href="{$U_FEED_IMAGE_ONLY}"> 
<link rel="alternate" type="application/rss+xml" title="{'Complete RSS feed (photos, comments)'|@translate}" href="{$U_FEED}"> 
{/html_head} 

{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content">

  <div class="titrePage">
    <ul class="categoryActions"></ul>
    <h2>{'Notification'|@translate}</h2>
  </div>

  <p>{'The RSS notification feed provides notification on news from this website : new photos, updated albums, new comments. Use a RSS feed reader.'|@translate}</p>

  <dl>
    <dt>
      <a href="{$U_FEED_IMAGE_ONLY}">{'Photos only RSS feed'|@translate}</a><br><br>
    </dt>
    <dt>
      <a href="{$U_FEED}">{'Complete RSS feed (photos, comments)'|@translate}</a>
    </dt>
  </dl>
</div>
