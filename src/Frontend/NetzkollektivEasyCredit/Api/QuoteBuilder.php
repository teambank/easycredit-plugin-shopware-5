<?php

namespace Shopware\Plugins\NetzkollektivEasyCredit\Api;

use Teambank\EasyCreditApiV3\Model\Transaction;
use Teambank\EasyCreditApiV3\Model\ShippingAddress;
use Teambank\EasyCreditApiV3\Model\InvoiceAddress;

class FakeCheckoutController extends \Shopware_Controllers_Frontend_Checkout
{
    public function __construct()
    {
        $this->init();
        $this->container = Shopware()->Container();
        $this->setView(new \Enlight_View_Default(
            Shopware()->Container()->get('template')
        ));
    }
}

class QuoteBuilder
{

    protected $basket;
    protected $user;
    protected $config;
    protected $storage;
    protected $helper;

    public function __construct()
    {
        $this->helper = new \EasyCredit_Helper();
        $this->basket = $this->helper->getBasket();
        $this->user = $this->helper->getUser();
        $this->config = Shopware()->Config();
        $this->storage = new Storage();
    }

    public function getId()
    {
        return \mb_substr(Shopware()->Session()->get('sessionId'), 0, 50);
    }

    public function getShippingMethod()
    {
        $dispatch = $this->helper->getSelectedDispatch();
        if (isset($dispatch['name'])) {
            $shippingMethod = strip_tags($dispatch['name']);
            if ($this->getIsClickAndCollect()) {
                $shippingMethod = '[Selbstabholung] ' . $shippingMethod;
            }
            return $shippingMethod;
        }
    }

    public function getIsClickAndCollect()
    {
        $dispatch = $this->helper->getSelectedDispatch();
        return (
            isset($dispatch['id'])
            && $dispatch['id'] == $this->config->get('easycreditClickAndCollectShippingMethod')
        );
    }

    protected $grandTotal;

    public function getGrandTotal()
    {
        if ($this->grandTotal === null) {
            /** @var \Shopware_Controllers_Frontend_Checkout $checkoutController */
            $checkoutController = new FakeCheckoutController();
            $checkoutController->setFront(Shopware()->Front());

            $basket = $checkoutController->getBasket();
            $this->grandTotal = empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
        }
        return $this->grandTotal;
    }

    public function getDuration()
    {
        return $this->storage->get('duration');
    }

    public function getInvoiceAddress()
    {
        $addressBuilder = new Quote\AddressBuilder();
        return $addressBuilder
            ->setAddressModel(new InvoiceAddress())
            ->build(
                $this->user,
                $this->user['billingaddress']
            );
    }
    public function getShippingAddress()
    {
        $addressBuilder = new Quote\AddressBuilder();
        return $addressBuilder
            ->setAddressModel(new ShippingAddress())
            ->build(
                $this->user,
                $this->user['shippingaddress']
            );
    }

    public function getCustomer()
    {
        return new Quote\CustomerBuilder();
    }

    public function getItems()
    {
        return $this->_getItems(
            $this->basket['content']
        );
    }

    protected function _getItems($items)
    {
        $_items = [];
        foreach ($items as $item) {
            $itemBuilder = new Quote\ItemBuilder($item);
            $quoteItem = $itemBuilder->build();
            if ($quoteItem->getPrice() <= 0) {
                continue;
            }
            $_items[] = $quoteItem;
        }

        return $_items;
    }

    public function getSystem()
    {
        $systemBuilder = new SystemBuilder();
        return $systemBuilder->build();
    }

    protected function _getUrl($action)
    {
        return Shopware()->Front()->Router()->assemble(array(
            'controller' => 'payment_easycredit',
            'action'    => $action,
        ));
    }

    protected function getRedirectLinks()
    {
        return new \Teambank\EasyCreditApiV3\Model\RedirectLinks([
            'urlSuccess' => $this->_getUrl('return'),
            'urlCancellation' => $this->_getUrl('cancel'),
            'urlDenial' => $this->_getUrl('reject')
        ]);
    }

    protected function isExpress()
    {
        return $this->storage->get('express');
    }

    public function build(): Transaction
    {
        $transaction = new Transaction([
            'paymentType' => $this->helper->getPaymentType($this->helper->getSelectedPayment()) . '_PAYMENT',
            'paymentSwitchPossible' => count($this->helper->getActivePaymentMethods()) > 1, // Switch between installment & bill payment should be possible if both methods are enabled
            'financingTerm' => $this->getDuration(),
            'orderDetails' => new \Teambank\EasyCreditApiV3\Model\OrderDetails([
                'orderValue' => $this->getGrandTotal(),
                'orderId' => $this->getId(),
                'numberOfProductsInShoppingCart' => 1,
                'invoiceAddress' => $this->isExpress() ? null : $this->getInvoiceAddress(),
                'shippingAddress' => $this->isExpress() ? null : $this->getShippingAddress(),
                'shoppingCartInformation' => $this->getItems(),
                'withoutFlexprice' => (new \EasyCredit_FlexpriceService())->shouldDisableFlexprice()
            ]),
            'shopsystem' => $this->getSystem(),
            'customer' => $this->getCustomer()->build(),
            'customerRelationship' => new \Teambank\EasyCreditApiV3\Model\CustomerRelationship([
                'customerSince' => ($this->getCustomer()->getCreatedAt() instanceof \DateTime) ? $this->getCustomer()->getCreatedAt()->format('Y-m-d') : null,
                'orderDoneWithLogin' => $this->getCustomer()->isLoggedIn(),
                'numberOfOrders' => $this->getCustomer()->getOrderCount(),
                'logisticsServiceProvider' => $this->getShippingMethod()
            ]),
            'redirectLinks' => $this->getRedirectLinks()
        ]);

        $transaction = $this->helper->getContainer()
            ->get('events')
            ->filter('NetzkollektivEasyCredit_Api_QuoteBuilder_Transaction', $transaction, ['subject' => $this]);

        return $transaction;
    }
}
