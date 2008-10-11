{* $Id$ *}
{known_script id="jquery" src=$ROOT_URL|@cat:"template-common/lib/jquery.packed.js"}
{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.resizable" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.resizable.packed.js"}

{* Resize possible *}
{literal}
<script type="text/javascript">
  jQuery().ready(function(){
    // Resize possible for double select list
    jQuery(".doubleSelect select.categoryList").resizable({
      handles: "w,e",
      animate: true,
      animateDuration: "slow",
      animateEasing: "swing",
      preventDefault: true,
      preserveCursor: true,
      autoHide: true,
      ghost: true
    });
  });
</script>
{/literal}
