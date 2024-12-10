<?php
class Shopware_Controllers_Frontend_PaymentEasycredit extends Shopware_Controllers_Frontend_Payment
{
    /**
     * @var Enlight_Components_Session_Namespace $session
     */
    private $session;

    private $order;

    private $em;

    private $helper;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->container = Shopware()->Container();
        $this->helper = new EasyCredit_Helper();
        $this->session = $this->container->get('session');
        $this->order = Shopware()->Modules()->Order();
        $this->em = Shopware()->Container()->get('models');
    }

    protected function getModule($moduleName)
    {
        return $this->container->get('modules')->getModule($moduleName);
    }

    public function getPaymentShortName()
    {
        if (($user = $this->getUser()) !== null
            && !empty($user['additional']['payment']['name'])
        ) {
            return $user['additional']['payment']['name'];
        } else {
            return null;
        }
    }

    public function createPaymentUniqueId()
    {
        if (class_exists('\Shopware\Components\Random')) {
            return \Shopware\Components\Random::getAlphanumericString(32);
        }
        return parent::createPaymentUniqueId();
    }

    public function getOrderId($transactionId, $paymentUniqueId)
    {
        $sql = '
            SELECT id FROM s_order
            WHERE transactionID=? AND temporaryID=?
            AND status!=-1
        ';
        return (int) Shopware()->Db()->fetchOne($sql, [
            $transactionId,
            $paymentUniqueId,
        ]);
    }

    public function indexAction()
    {
        try {
            $transactionId = $this->helper->getPluginSession()["transaction_id"];
            $paymentUniqueId = $this->createPaymentUniqueId();

            $orderNumber = $this->saveOrder($transactionId, $paymentUniqueId, null, false);
            $orderId = $this->getOrderId($transactionId, $paymentUniqueId);

            $this->saveOrderAttributes($orderId);

            $checkout = $this->container->get('easyCreditCheckout');
            if (!$checkout->authorize($orderNumber)) {
                throw new \Exception('The transaction could not be authorized.');
            }

            $tx = $checkout->loadTransaction();

            if ($tx->getStatus() !== \Teambank\EasyCreditApiV3\Model\TransactionInformation::STATUS_AUTHORIZED) {
                throw new \Exception('The transaction could not be authorized.');
            }

            $paymentStatusId = $this->helper->getPlugin()->Config()->get('easycreditPaymentStatus');
            $this->savePaymentStatus($transactionId, $paymentUniqueId, $paymentStatusId, false);
            $this->setPaymentClearedDate($transactionId);

            $this->redirect(array(
                'controller' => 'checkout',
                'action' => 'finish',
                'sUniqueID' => $paymentUniqueId
            ));
        } catch (\Exception $e) {
            $orderStatusId = $this->helper->getPlugin()->Config()->get('easycreditOrderErrorStatus');
            $this->order->setOrderStatus($orderId, $orderStatusId, true, $e->getMessage());

            $this->container->get('pluginlogger')->error($e->getMessage());
            $this->helper->getPlugin()->getStorage()->set('apiError', 'Die Zahlung mit <strong>easyCredit</strong> konnte auf Grund eines Fehlers nicht abgeschlossen werden. Bitte probieren Sie es erneut oder kontaktieren Sie den Händler.');
            $this->helper->getPlugin()->getStorage()->set('apiErrorSkipSuffix', true);
            $this->redirect(array(
                'controller' => 'checkout',
                'action' => 'cart'
            ));
            return;
        }
    }

    public function returnAction()
    {
        try {
            $checkout = $this->container->get('easyCreditCheckout');
            $transaction = $checkout->loadTransaction();
            $approved = $checkout->isApproved();

            if (!$approved) {
                throw new \Exception('easyCredit-Ratenkauf wurde nicht genehmigt.');
            }

            if ($this->helper->getPlugin()->getStorage()->get('express')) {
                $customerService = new EasyCredit_CustomerService();
                $customer = $customerService->createCustomer($transaction);
                $customerService->loginCustomer($customer);
            }

            $this->updatePaymentMethod($transaction->getTransaction()->getPaymentType());

            $this->helper->getPlugin()->addInterest();
            $this->redirectCheckoutConfirm();
        } catch (Exception $e) {
            $this->helper->getPlugin()->getStorage()->set('apiError', $e->getMessage());
            $this->_redirToPaymentSelection();
            return;
        }

        if (!$approved) {
            $this->helper->getPlugin()->getStorage()->set('apiError', 'easyCredit-Ratenkauf wurde nicht genehmigt.');
            $this->_redirToPaymentSelection();
            return;
        }

        if ($this->helper->getPlugin()->getStorage()->get('express')) {
            $customerService = new EasyCredit_CustomerService();
            $customer = $customerService->createCustomer($transaction);
            $customerService->loginCustomer($customer);

            $this->helper->getPlugin()->getStorage()->set('express', false);
            $checkout->finalizeExpress($this->helper->getPlugin()->getQuote());
        }

        $this->helper->getPlugin()->addInterest();
        $this->redirectCheckoutConfirm();
    }

    public function createOrder($transaction)
    {
        $customerService = new EasyCredit_CustomerService();
        $customerService->createCustomer($transaction);
    }

    protected function updatePaymentMethod($paymentType)
    {
        $paymentMethod = $this->helper->getMethodByPaymentType(
            $paymentType
        );
        if (!$paymentMethod->getActive()) {
            throw new \Exception($paymentMethod->getDescription() . ' ist derzeit nicht verfügbar. Bitte wählen Sie eine andere Zahlungsart.');
        }
        $this->session->offsetSet('sPaymentID', $paymentMethod->getId());
        $this->getModule('admin')->sUpdatePayment($paymentMethod->getId());
    }

    public function cancelAction()
    {
        $this->_redirToPaymentSelection();
    }

    public function rejectAction()
    {
        $this->_redirToPaymentSelection();
    }

    public function expressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $this->helper->getPlugin()->clear();

        $basket = $this->getModule('basket');
        if ($productNumber = $this->Request()->getParam('sAdd')) {
            $basket->sDeleteBasket();
            $basket->sAddArticle($productNumber, (int) $this->Request()->getParam('sQuantity', 1));
        }

        $params = $this->Request()->getParam('easycredit');

        $this->updatePaymentMethod($params['paymentType']);

        if (isset($params['numberOfInstallments']) && $params['numberOfInstallments'] > 0) {
            $this->helper->getPlugin()
                ->getStorage()
                ->set('duration', $params['numberOfInstallments']);
        }

        $checkoutController = $this->getCheckoutController();
        $checkoutController->getSelectedCountry();
        $checkoutController->getSelectedDispatch();

        /** @var sAdmin $admin */
        $admin = $this->getModule('admin');
        $countries = $admin->sGetCountryList();
        $admin->sGetPremiumShippingcosts(\reset($countries));

        $basket->sGetBasket();
        $this->helper->getPlugin()->getStorage()->set('express', true);
    }

    public function respondWithStatus($content, $code = 200)
    {
        http_response_code($code);
        echo $content;
        exit; // NOSONAR
    }

    /**
     * @return Shopware_Controllers_Frontend_Checkout
     */
    private function getCheckoutController()
    {
        /** @var Shopware_Controllers_Frontend_Checkout $checkoutController */
        $checkoutController = Enlight_Class::Instance(Shopware_Controllers_Frontend_Checkout::class, [$this->request, $this->response]);
        $checkoutController->init();
        $checkoutController->setView($this->View());
        $checkoutController->setContainer($this->container);
        $checkoutController->setFront($this->front);

        return $checkoutController;
    }

    protected function setPaymentClearedDate($transactionId)
    {
        if (defined('\Shopware::VERSION') && version_compare(\Shopware::VERSION, '5.1.0') == -1) {
            return;
        }

        $sql = "
            UPDATE s_order as o
            INNER JOIN s_core_states AS s
                   ON s.id = o.cleared
            SET o.cleareddate = NOW()
            WHERE o.cleareddate IS NULL
            AND s.name = 'completely_paid'
            AND o.transactionID = ?;
        ";
        $this->container->get('db')->query($sql, array($transactionId));
    }

    public function addInterestSurcharge()
    {
        if (
            !isset($this->helper->getPluginSession()["interest_amount"])
            || empty($this->helper->getPluginSession()["interest_amount"])
        ) {
            return;
        }

        $interest_order_name = 'sw-payment-ec-interest';

        $interest_amount = round($this->helper->getPluginSession()["interest_amount"], 2);

        $this->container->get('db')->delete(
            's_order_basket',
            array(
                'sessionID = ?' => $this->session->get('sessionId'),
                'ordernumber = ?' => $interest_order_name
            )
        );

        $this->container->get('db')->insert(
            's_order_basket',
            array(
                'sessionID' => $this->session->offsetGet('sessionId'),
                'articlename' => 'Zinsen für Ratenzahlung',
                'articleID' => 0,
                'ordernumber' => $interest_order_name,
                'quantity' => 1,
                'price' => $interest_amount,
                'netprice' => $interest_amount,
                'tax_rate' => 0,
                'datum' => new Zend_Date(),
                'modus' => 4,
                'currencyFactor' => 1
            )
        );
    }

    public function saveOrderAttributes($orderId)
    {
        $orderAttributeModel = $this->em->getRepository('Shopware\Models\Attribute\Order')->findOneBy(
            array('orderId' => $orderId)
        );

        if ($orderAttributeModel instanceof \Shopware\Models\Attribute\Order) {
            $orderAttributeModel->setEasycreditToken($this->helper->getPluginSession()["token"]);
            $this->em->persist($orderAttributeModel);
            $this->em->flush();
        }
    }

    public function redirectCheckoutConfirm()
    {
        $this->redirect(
            array(
                'controller' => 'checkout',
                'action' => 'confirm',
            )
        );
    }

    protected function _redirToPaymentSelection()
    {
        $this->redirect(array(
            'controller' => 'checkout',
            'action' => 'shippingPayment'
        ));
    }
}
