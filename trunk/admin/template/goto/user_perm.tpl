{* $Id$ *}
<h2>{$TITLE}</h2>

{if isset($categories_because_of_groups) }
<fieldset>
  <legend>{'Categories authorized thanks to group associations'|@translate}</legend>

  <ul>
    {foreach from=$categories_because_of_groups item=cat }
    <li>{$cat}</li>
    {/foreach}
  </ul>
</fieldset>
{/if}


<fieldset>
  <legend>{'Other private categories'|@translate}</legend>

  <form method="post" action="{$F_ACTION}">
    {$DOUBLE_SELECT}
  </form>
</fieldset>
