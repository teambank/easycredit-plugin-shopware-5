{extends file='parent:frontend/checkout/ajax_cart.tpl'}

{block name='frontend_checkout_ajax_cart_button_container_inner'}

    {$smarty.block.parent}

    {block name='frontend_checkout_ajax_cart_easycredit_express'}
        {if $EasyCreditExpressOffCanvas}
            {include file='frontend/plugins/payment/easycredit/express-button.tpl' amount="{$sBasket.AmountNumeric}"}
        {/if}
    {/block}

    {block name='frontend_checkout_ajax_cart_easycredit_widget'}
        {if $EasyCreditWidgetOffCanvas}
        <div class="easycredit-widget-container">
            <easycredit-widget 
                webshop-id="{$EasyCreditApiKey}" 
                amount="{$sBasket.AmountNumeric}"
                payment-types="{', '|implode:$EasyCreditActiveMethods}"
            ></easycredit-widget>
        </div>
        {/if}
    {/block}

{/block}
