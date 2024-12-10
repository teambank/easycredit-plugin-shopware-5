{block name='easycredit_express_button_container'}
    <div class="easycredit-express-button-container" data-url="{url controller=payment_easycredit action=express}">
        <easycredit-express-button 
            webshop-id="{$EasyCreditApiKey}" 
            amount="{$amount}"
            payment-types="{', '|implode:$EasyCreditActiveMethods}"
        ></easycredit-express-button>
    </div>
{/block}
