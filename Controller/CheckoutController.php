<?php
//TODO need Paypal dependance

namespace Dywee\OrderCMSBundle\Controller;

use Dywee\AddressBundle\Entity\Address;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Dywee\OrderCMSBundle\Form\ShippingAddressType;
use Dywee\OrderCMSBundle\Form\BillingAddressType;
use Dywee\ProductBundle\Entity\Product;
use Dywee\ProductBundle\Entity\ProductSubscription;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class CheckoutController extends Controller
{
    /**
     * @param Address $address
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route(name="checkout_billing", path="checkout/billing")
     */
    public function billingAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        if($order->countProducts() > 0)
        {
            $em = $this->getDoctrine()->getManager();

            $billingAddress = $order->getBillingAddress() ?? new Address();

            $form = $this->createForm(BillingAddressType::class, $billingAddress);

            if($form->handleRequest($request)->isValid())
            {
                $order->setBillingAddress($billingAddress);

                $em->persist($order);
                $em->flush();

                //$this->get('event_dispatcher')->dispatch(DyweeOrderCMSEvent::VALID_BILLING, $checkoutStatEvent->setAuthentified(false));
                $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::VALID_BILLING);

                return $this->redirect($this->generateUrl('checkout_shipping'));
            }

            //$this->get('event_dispatcher')->dispatch(DyweeOrderCMSEvent::DISPLAY_BILLING, $checkoutStatEvent);
            $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::DISPLAY_BILLING);

            return $this->render('DyweeOrderCMSBundle:Billing:billing.html.twig',
                array(
                    'form' => $form->createView(),
                    //TODO rendre dynamique
                    'orderConnexionPermission' => 'both'
                )
            );
        }
        else {
            $this->addFlash('warning', 'votre session a expirée');
            return $this->redirectToRoute('basket_view');
        }

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route(name="checkout_shipping", path="checkout/shipping")
     */
    public function shippingAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        if($order->countProducts() > 0) {

            $em = $this->getDoctrine()->getManager();
            //$ar = $em->getRepository('DyweeAddressBundle:Address');

            $billingAddress = $order->getBillingAddress();

            $em->detach($billingAddress);

            $shippingAddress = $billingAddress;

            $form = $this->createForm(ShippingAddressType::class, $shippingAddress);
            if($order->getShippingMessage())
                $form->get('shippingMessage')->setData($order->getShippingMessage());


            if ($form->handleRequest($request)->isValid()) {
                $order->setShippingAddress($shippingAddress);

                $order->setShippingMessage($form->get('shippingMessage')->getData());

                $em->persist($order);
                $em->flush();

                $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::VALID_SHIPPING);
                return $this->redirect($this->generateUrl('checkout_shipping_method'));
            }


            //$checkoutStatEvent = new CheckoutStatEvent($order, $this->getUser(), DyweeOrderCMSEvent::DISPLAY_SHIPPING);
            //$this->get('event_dispatcher')->dispatch(DyweeOrderCMSEvent::DISPLAY_SHIPPING, $checkoutStatEvent);

            $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::DISPLAY_SHIPPING);

            return $this->render('DyweeOrderCMSBundle:Shipping:shipping.html.twig', array(
                'home' => $form->createView()
            ));
        }
        else {
            $this->addFlash('warning', 'votre session a expirée');
            return $this->redirectToRoute('basket_view');
        }
    }


    /*  OLD METHOD
     public function shippingAction($address_id = null, Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        $em = $this->getDoctrine()->getManager();
        $smr = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');
        $ar = $em->getRepository('DyweeAddressBundle:Address');

        if(is_numeric($address_id))
        {
            $shippingAddress = $ar->findOneById($address_id);
            if($shippingAddress != null && $shippingAddress->getUser() == $this->getUser())
            {
                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');
                $order->setShippingUser($this->getUser());

                $em->persist($order);
                $em->flush();

                return $this->shippingAction($order, 'HomMethod');
            }
            else throw new AccessDeniedException('Vous ne pouvez pas sélectionner cette addresse');
        }
        $billingAddress = $order->getBillingAddress();


        $liv24R = false;
        $livHOM = false;

        foreach($order->getOrderElements() as $orderElement)
        {
            $shipmentMethods = $smr->myfindBy($order->getBillingAddress()->getCity()->getCountry(), $orderElement->getProduct()->getWeight());
            foreach($shipmentMethods as $shipmentMethod)
            {
                if($shipmentMethod->getType() == '24R')
                {
                    $liv24R = true;
                    $this->get('session')->set('24RMethod', $shipmentMethod);
                }

                else if($shipmentMethod->getType() == 'HOM')
                {
                    $livHOM = true;
                    $this->get('session')->set('HomMethod', $shipmentMethod);
                }
            }
        }

        //Tierce FORM
        $shippingTierceAddress = new Address();
        $formTierce = $this->createForm(ShippingAddressType::class, $shippingTierceAddress);

        //Tierce Form

        $data = array('tierce' => $formTierce->createView());

        // HOM FORM
        if($livHOM)
        {
            $shippingAddress = clone $billingAddress;

            $formHome = $this->createForm(ShippingAddressType::class, $shippingAddress);

            if(isset($formHome) && $formHome->handleRequest($request)->isValid()) {
                $data = $formHome->getData();

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');

                return $this->step4Action($order, 'HomMethod');
            }
            else $data['home'] = $formHome->createView();
        }

        //24R FORM
        if($liv24R)
        {
            $formMR = $this->createForm(array())
                ->add('country',   'entity', array(
                    'class' => 'DyweeAddressBundle:Country',
                    'property' => 'name',
                    'required' => false,
                    'data' => $shippingAddress->getCity()->getCountry()
                ))
                ->add('zip', 'text', array('required' => false, 'data' => $shippingAddress->getCity()->getZip()))
                ->add('ref',   'hidden')
                ->add('mrSave', 'submit')
                ->getForm();

            if($formMR->handleRequest($request)->isValid())
            {
                $cr = $em->getRepository('DyweeAddressBundle:Country');
                $data = $formMR->getData();

                $country = $cr->findOneById($data['country']->getId());

                $shippingAddress->setCountry($country);

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('24R');
                $order->setDeliveryInfo($data['ref']);

                return $this->step4Action($order, '24RMethod');
            }
            else $data['mr'] = $formMR->createView();
        }
        if(isset($formTierce) && $formTierce->handleRequest($request)->isValid())
        {
            $order->setShippingAddress($shippingTierceAddress);
            $order->setIsGift(true);

            $order->setShippingMessage($formTierce->get('shippingMessage')->getData());

            $em->persist($order);
            $em->flush();

            $request->getSession()->set('order', $order);

            return $this->redirect($this->generateUrl('dywee_basket_step3b'));
        }
        $data['step'] = 2;
        //TODO rendre dynamique
        $data['orderConnexionPermission'] = 'both'; //$this->container->getParameter('dywee_order_bundle.orderConnexionPermission');
        return $this->render('DyweeOrderCMSBundle:Checkout:shipping.html.twig', $data);
    }*/

    /**
     *
     * @Route(name="checkout_shipping_third_person", path="checkout/shipping/third")
     */
    public function thirdPersonShippingAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        $em = $this->getDoctrine()->getManager();
        $smr = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');

        $shipmentMethods = $smr->myfindBy($order->getShippingAddress()->getCity()->getCountry(), $order->getWeight());

        $liv24R = false;
        $livHOM = false;


        foreach($shipmentMethods as $shipmentMethod)
        {
            if($shipmentMethod->getType() == '24R')
            {
                $liv24R = true;
                $this->get('session')->set('24RMethod', $shipmentMethod);
            }

            else if($shipmentMethod->getType() == 'HOM')
            {
                $livHOM = true;
                $this->get('session')->set('HomMethod', $shipmentMethod);
            }
        }

        // HOM FORM
        if($livHOM)
        {
            $shippingAddress = $order->getShippingAddress();

            $formHome = $this->get('form.factory')->create(new ShippingAddressType(), $shippingAddress);
            $formHome->remove('email')->add('email', 'repeated', array(
                'type' => 'email',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Adresse e-mail'),
                'second_options' => array('label' => 'Confirmer Adresse e-mail')
            ))
                ->add('other', 'text', array('required' => false));

            if(isset($formHome) && $formHome->handleRequest($request)->isValid())
            {
                $data = $formHome->getData();

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');

                return $this->step4Action($order, 'HomMethod');
            }
            else $data['home'] = $formHome->createView();
        }

        //24R FORM
        if($liv24R)
        {
            $formMR = $this->createForm(array())
                ->add('country',   'entity', array(
                    'class' => 'DyweeAddressBundle:Country',
                    'property' => 'name',
                    'required' => false,
                    'data' => $shippingAddress->getCity()->getCountry()
                ))
                ->add('zip', 'text', array('required' => false, 'data' => $shippingAddress->getCity()->getZip()))
                ->add('ref',   'hidden')
                ->add('mrSave', 'submit')
                ->getForm();

            if($formMR->handleRequest($request)->isValid()) {
                $cr = $em->getRepository('DyweeAddressBundle:Country');
                $data = $formMR->getData();

                $country = $cr->findOneById($data['country']->getId());

                $shippingAddress->setCountry($country);

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('24R');
                $order->setDeliveryInfo($data['ref']);

                return $this->step4Action($order, '24RMethod');
            }
            else $data['mr'] = $formMR->createView();
        }
        $data['step'] = 3;
        return $this->render('DyweeOrderBundle:Basket:shipping.html.twig', $data);
    }

    /**
     *
     * @Route(name="checkout_shipping_method", path="checkout/shipping/method")
     */
    public function shippingMethodAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        $shippingMethods = $this->get('dywee_order.shipment_method')->calculateForOrder($order);

        if(!$shippingMethods)
            throw $this->createNotFoundException($this->get('dywee_order.shipment_method')->getError());

        $form = $this->createFormBuilder(array())->add('shippingMethod', ChoiceType::class, array(
            'choices' => $shippingMethods,
            'label' => 'checkout.shipping_method',
            'expanded' => true
        ))->getForm();

        if($form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $shippingMethodRepository = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');
            $shippingMethod = $shippingMethodRepository->findOneById($form->getData()['shippingMethod']);

            $order->setShippingMethod($shippingMethod);
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('checkout_overview'));
        }

        $checkoutStatEvent = new CheckoutStatEvent($order, $this->getUser(), DyweeOrderCMSEvent::DISPLAY_SHIPPING_METHODS);

        $this->get('event_dispatcher')->dispatch(DyweeOrderCMSEvent::DISPLAY_SHIPPING_METHODS, $checkoutStatEvent);

        return $this->render('DyweeOrderCMSBundle:Shipping:shipping_methods.html.twig', array(
            'shipping_methods' => $shippingMethods,
            'order' => $order,
            'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @Route(name="checkout_overview", path="checkout/overview")
     *
     */
    public function overviewAction()
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        $data = array('order' => $order);

        //TODO a bouger dans une gestion "shipment"
        /*if($order->getDeliveryMethod() == '24R')
        {
            $client = new \nusoap_client('http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL', true);

            $explode = explode('-', $order->getDeliveryInfo());

            $params = array(
                'Enseigne' => "BEBLCBLC",
                'Num' => $explode[1],
                'Pays' => $explode[0]
            );

            $security = '';
            foreach($params as $param)
                $security .= $param;
            $security .= 'xgG1mpth';

            $params['Security'] = strtoupper(md5($security));

            $result = $client->call('WSI2_AdressePointRelais', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_AdressePointRelais');

            if($result['WSI2_AdressePointRelaisResult']['STAT'] == 0)
            {
                $data['relais'] = array(
                    'address1'  => $result['WSI2_AdressePointRelaisResult']['LgAdr1'],
                    'address2'  => $result['WSI2_AdressePointRelaisResult']['LgAdr3'],
                    'zip'       => $result['WSI2_AdressePointRelaisResult']['CP'],
                    'cityString' => $result['WSI2_AdressePointRelaisResult']['Ville']
                );
            }
            else throw $this->createNotFoundException('Erreur dans la recherche du point relais');
        }*/

        $checkoutStatEvent = new CheckoutStatEvent($order, $this->getUser(), DyweeOrderCMSEvent::DISPLAY_RECAP);

        $this->get('event_dispatcher')->dispatch(DyweeOrderCMSEvent::DISPLAY_RECAP, $checkoutStatEvent);

        return $this->render('DyweeOrderCMSBundle:Checkout:recap.html.twig', $data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route(name="paypal_checkout", path="paypal/checkout")
     */
    public function paypalCheckoutAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $request = $this->container->get('request');
            if($request->isXmlHttpRequest()) {
                $response = new Response();
                $approvalUrl = $this->paypalSetupAction();
                if (isset($approvalUrl)) {
                    $response->setContent(json_encode(array(
                        'type' => 'success',
                        'url' => $approvalUrl
                    )));
                } else $response->setContent(json_encode(array(
                    'Error' => 'Pays invalide',
                )));

                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            else throw $this->createNotFoundException('Requete invalide');
        }

        $approvalUrl = $this->paypalSetupAction();
        if(isset($approvalUrl))
            return $this->redirect($approvalUrl);
    }

    /**
     * @return mixed
     *
     * @Route(name="paypal_setup", path="paypal/setup")
     * TODO a bouger dans un bundle payment
     */
    public function paypalSetupAction()
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();
        $address = $order->getShippingAddress();

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $shippingAddress = new ShippingAddress();
        $shippingAddress->setRecipientName($address->getLastName().' '.$address->getFirstName());
        $shippingAddress->setLine1($address->getAddress1());
        $shippingAddress->setCity($address->getCityString());
        $shippingAddress->setPhone($address->getMobile());

        $items = array();
        foreach($order->getOrderElements() as $orderElement)
        {
            $item = new Item();

            $item->setName($orderElement->getProduct()->getName());
            $item->setCurrency('EUR');
            $item->setQuantity($orderElement->getQuantity());
            $item->setPrice((string)number_format($orderElement->getUnitPrice(), 2, '.', ''));

            $items[] = $item;
        }

        $itemList = new ItemList();
        $itemList->setItems($items);
        //$itemList->setShippingAddress($shippingAddress);


        $details = new Details();
        $details->setShipping((string) number_format($order->getShippingCost(), 2, '.', ''))
            ->setTax(0)
            ->setSubtotal((string) number_format($order->getPriceVatIncl(), 2, '.', ''));

        $amount = new Amount();
        $amount->setCurrency('EUR')
            ->setTotal($order->getTotalPrice())
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('Vente au profit de la Belgique une fois');

        $baseUrl = 'http://www.labelgiqueunefois.com/'.$this->get('request')->getBasePath();
        $redirectUrls = new RedirectUrls();


        $redirectUrls->setReturnUrl('http://www.labelgiqueunefois.com/fr/'."paypal-confirmation/success/".$order->getReference())
            ->setCancelUrl('http://www.labelgiqueunefois.com/fr/'."paypal-confirmation/cancelled/".$order->getReference());

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        $payment->setExperienceProfileId($this->container->getParameter('paypal.experienceProfileId')); //live


        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->container->getParameter('paypal.clientID'),
                $this->container->getParameter('paypal.clientSecret')
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => $this->container->getParameter('paypal.mode'),
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => $this->container->getParameter('paypal.logLevel'), // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS, DEBUG for testing
                'validation.level' => 'log',
                'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            )
        );

        try {
            $payment->create($apiContext);
        } catch (\Exception $ex) {
            throw $this->createException('Something went wrong (Paypal Exception)');
        }
        $approvalUrl = $payment->getApprovalLink();

        $this->get('session')->set('paymentId', $payment->getId());

        $order->setPayementInfos($payment->getId());
        $order->setPayementMethod(3);
        $order->setPayementState(0);


        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($order);
        $em->flush();

        if(isset($approvalUrl))
            return $approvalUrl;
    }
}
