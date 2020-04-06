// {block name="backend/easycredit_merchant/model/order"}
Ext.define('Shopware.apps.EasycreditMerchant.model.Order', {

    /**
     * @type { String }
     */
    extend: 'Shopware.data.Model',

    /**
     * The fields used for this model
     * @type { Array }
     */
    fields: [
        // {block name="backend/easycredit_merchant/model/order/fields"}{/block}
        { name: 'id', type: 'string' },
        { name: 'create_time', type: 'string' },
        { name: 'update_time', type: 'string' },
        { name: 'state', type: 'string' },
        { name: 'payment_mode', type: 'string' }
    ]
});
// {/block}