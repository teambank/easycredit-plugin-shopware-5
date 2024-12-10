{extends file='parent:frontend/detail/buy.tpl'}

{block name='frontend_detail_buy_button'}
    {$smarty.block.parent}

    {block name='frontend_detail_buy_button_easycredit_express'}
        {if !($sArticle.sConfigurator && !$activeConfiguratorSelection) && $EasyCreditExpressProduct}
            {include file='frontend/plugins/payment/easycredit/express-button.tpl' amount=$sArticle.price_numeric}
        {/if}
    {/block}
{/block}
