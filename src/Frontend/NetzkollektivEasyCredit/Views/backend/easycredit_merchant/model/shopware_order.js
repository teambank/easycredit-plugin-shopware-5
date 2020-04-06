// {block name="backend/easycredit_merchant/model/shopware_order"}
Ext.define('Shopware.apps.EasycreditMerchant.model.ShopwareOrder', {

    /**
     * @type { String }
     */
    extend: 'Shopware.data.Model',

    /**
     * The fields used for this model
     * @type { Array }
     */
    fields: [
        // {block name="backend/easycredit_merchant/model/shopware_order/fields"}{/block}
        { name: 'number', type: 'string' },
        { name: 'invoiceAmount', type: 'float' },
        { name: 'languageIso', type: 'string' },
        { name: 'customerId', type: 'string' },
        { name: 'orderTime', type: 'datetime' },
        { name: 'status', type: 'string' },
        { name: 'cleared', type: 'string' },
        { name: 'paymentId', type: 'string' },
        { name: 'currency', type: 'string' },
        { name: 'transactionId', type: 'string' },
        { name: 'temporaryId', type: 'string' },
        { name: 'paymentType', mapping: 'attribute.swagEasycreditMerchantPaymentType' },
        { name: 'merchantStatus', mapping: 'transactionId' }
    ],

    /**
     * @type { Array }
     */
    associations: [
        { type: 'hasMany', model: 'Shopware.apps.Base.model.Customer', name: 'getCustomer', associationKey: 'customer' },
        { type: 'hasMany', model: 'Shopware.apps.Base.model.Shop', name: 'getLanguageSubShop', associationKey: 'languageSubShop' },
        { type: 'hasMany', model: 'Shopware.apps.Base.model.OrderStatus', name: 'getOrderStatus', associationKey: 'orderStatus' },
        { type: 'hasMany', model: 'Shopware.apps.Base.model.Payment', name: 'getPayment', associationKey: 'payment' },
        { type: 'hasMany', model: 'Shopware.apps.Base.model.PaymentStatus', name: 'getPaymentStatus', associationKey: 'paymentStatus' }
    ]
});
// {/block}