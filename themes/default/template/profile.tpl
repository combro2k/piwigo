{if isset($MENUBAR)}{$MENUBAR}{/if}
<div id="content" class="content{if isset($MENUBAR)} contentWithMenu{/if}">

<div class="titrePage">
	<ul class="categoryActions">
		<li><a href="{$U_HOME}" title="{'Home'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-home">&nbsp;</span><span class="pwg-button-text">{'Home'|@translate}</span>
		</a></li>
  </ul>
	<h2>{'Profile'|@translate}</h2>
</div>

{include file='infos_errors.tpl'}

{$PROFILE_CONTENT}
</div> <!-- content -->
