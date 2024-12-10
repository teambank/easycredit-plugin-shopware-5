{extends file='parent:frontend/checkout/cart.tpl'}

{block name='frontend_checkout_cart_premium'}
    {block name='frontend_checkout_cart_premium_easycredit_widget'}
        {if $EasyCreditWidgetCart}
        <div class="easycredit-widget-container">
            <easycredit-widget 
                webshop-id="{$EasyCreditApiKey}" 
                amount="{$sBasket.AmountNumeric}" 
                payment-types="{', '|implode:$EasyCreditActiveMethods}"
                {if $EasyCreditDisableFlexprice}disable-flexprice="true"{/if}
            ></easycredit-widget>
        </div>
        {/if}
    {/block}

    {$smarty.block.parent}
{/block}

{block name='frontend_checkout_cart_table_actions'}
    {$smarty.block.parent}

    {block name='frontend_checkout_cart_table_actions_easycredit_express'}
        {if $EasyCreditExpressCart}
            {include file='frontend/plugins/payment/easycredit/express-button.tpl' amount="{$sBasket.AmountNumeric}"}
        {/if}
    {/block}
{/block}
