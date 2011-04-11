{footer_script}
var incompatible_msg = '{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'javascript'}';
incompatible_msg += '\n';
incompatible_msg += '{'Do you want to activate anyway?'|@translate|@escape:'javascript'}';

{literal}
jQuery(document).ready(function() {
  jQuery('.incompatible').click(function() {
    return confirm(incompatible_msg);
  });
  jQuery('.warning').tipTip({
    'delay' : 0,
    'fadeIn' : 200,
    'fadeOut' : 200
  });
});
{/literal}{/footer_script}

<div class="titrePage">
  <h2>{'Plugins'|@translate}</h2>
</div>

{if isset($plugins)}

{foreach from=$plugin_states item=plugin_state}
<fieldset>
  <legend>
  {if $plugin_state == 'active'}
  {'Active Plugins'|@translate}

  {elseif $plugin_state == 'inactive'}
  {'Inactive Plugins'|@translate}

  {elseif $plugin_state == 'missing'}
  {'Missing Plugins'|@translate}

  {elseif $plugin_state == 'merged'}
  {'Obsolete Plugins'|@translate}

  {/if}
  </legend>
  {foreach from=$plugins item=plugin name=plugins_loop}
    {if $plugin.STATE == $plugin_state}
  <div class="pluginBox">
    <table>
      <tr>
        <td class="pluginBoxNameCell{if $plugin.INCOMPATIBLE} warning" title="{'WARNING! This plugin does not seem to be compatible with this version of Piwigo.'|@translate|@escape:'html'}{/if}">
          {$plugin.NAME}
        </td>
        <td>{$plugin.DESC}</td>
      </tr>
      <tr>
        <td>
    {if $plugin.STATE == 'active'}
          <a href="{$plugin.U_ACTION}&amp;action=deactivate">{'Deactivate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=restore" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Restore'|@translate}</a>

    {elseif $plugin_state == 'inactive'}
          <a href="{$plugin.U_ACTION}&amp;action=activate" {if $plugin.INCOMPATIBLE}class="incompatible"{/if}>{'Activate'|@translate}</a>
          | <a href="{$plugin.U_ACTION}&amp;action=delete" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Delete'|@translate}</a>

    {elseif $plugin_state == 'missing'}
          <a href="{$plugin.U_ACTION}&amp;action=uninstall" onclick="return confirm('{'Are you sure?'|@translate|@escape:'javascript'}');">{'Uninstall'|@translate}</a>

    {elseif $plugin_state == 'merged'}
          <a href="{$plugin.U_ACTION}&amp;action=delete">{'Delete'|@translate}</a>
    {/if}
        </td>
        <td>
          {'Version'|@translate} {$plugin.VERSION}
    {if not empty($plugin.AUTHOR)}
      {if not empty($plugin.AUTHOR_URL)}
        {assign var='author' value='<a href="%s">%s</a>'|@sprintf:$plugin.AUTHOR_URL:$plugin.AUTHOR}
      {else}
        {assign var='author' value=$plugin.AUTHOR}
      {/if}
          | {'By %s'|@translate|@sprintf:$author}
    {/if}

    {if not empty($plugin.VISIT_URL)}
          | <a class="externalLink" href="{$plugin.VISIT_URL}">{'Visit plugin site'|@translate}</a>
    {/if}
        </td>
      </tr>
    </table>
  </div>
    {/if}
  {/foreach}
</fieldset>
{/foreach}

{/if}
