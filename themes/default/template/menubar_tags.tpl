<dt>{'Related tags'|@translate}</dt>
<dd>
	<div id="menuTagCloud">
		{foreach from=$block->data item=tag}
		<span>
			<a class="tagLevel{$tag.level}"
			{if isset($tag.U_ADD)}
				href="{$tag.U_ADD}"
				title="{$pwg->l10n_dec('%d image is also linked to current tags', '%d images are also linked to current tags', $tag.counter)}"
				rel="nofollow">+
			{else}
				href="{$tag.URL}"
				title="{'See images linked to this tag only'|@translate}">
			{/if}
				{$tag.name}</a></span>
{* ABOVE there should be no space between text, </a> and </span> elements to avoid IE8 bug https://connect.microsoft.com/IE/feedback/ViewFeedback.aspx?FeedbackID=366567 *}
		{/foreach}
	</div>
</dd>
