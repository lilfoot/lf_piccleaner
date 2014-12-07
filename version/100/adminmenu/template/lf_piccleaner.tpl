{if $step == "overview"}
    {assign var=cTPLPfad value="`$oPlugin->cAdminmenuPfad`template/tpl_inc/overview.tpl"}
    {include file="$cTPLPfad"}
{/if}