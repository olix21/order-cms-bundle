<?php

namespace Dywee\OrderCMSBundle\Controller;

use Dywee\AddressBundle\Entity\Address;
use Dywee\CoreBundle\Model\AddressInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Entity\ShippingMethod;
use Dywee\OrderBundle\Form\ShippingOptionsType;
use Dywee\OrderBundle\Service\OrderVirtualizationManager;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Dywee\OrderCMSBundle\Form\ShippingAddressType;
use Dywee\OrderCMSBundle\Form\BillingAddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CheckoutController extends AbstractController
{
    /**
     * @Route(name="checkout_billing", path="checkout/billing", defaults={"address": null})
     * @Route(name="checkout_billing_address_selected", path="checkout/billing/address/{id}")
     *
     * @param Address|null $address
     * @param Request      $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function billingAction(Address $address = null, Request $request)
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        if ($order->countProducts() > 0) {
            // If no address provided: form
            if (!$address) {
                $address = $order->getBillingAddress() ?? new Address();

                $form = $this->createForm(BillingAddressType::class, $address);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    if ($this->getUser()) {
                        $address->setUser($this->getUser());
                    }
                } else {
                    $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::DISPLAY_BILLING);

                    return $this->render(
                        'DyweeOrderCMSBundle:Billing:billing.html.twig',
                        [
                            'form'                     => $form->createView(),
                            //TODO rendre dynamique
                            'orderConnexionPermission' => 'both'
                        ]
                    );
                }
            }

            // Handling of provided address / valid form
            if ($address) {
                $order->setBillingAddress($address);

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();

                $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::VALID_BILLING);

                if ($this->get(OrderVirtualizationManager::class)->isFullyVirtual($order)) {
                    //handle free shipping
                    return $this->redirectToRoute('checkout_overview');
                }
                return $this->redirectToRoute('checkout_shipping');
            }
        } else {
            $this->addFlash('warning', 'votre session a expirée');

            return $this->redirectToRoute('basket_view');
        }
    }

    /**
     * @Route(name="checkout_shipping", path="checkout/shipping")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function shippingAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        if ($order->countProducts() > 0) {
            $em = $this->getDoctrine()->getManager();

            $billingAddress = $order->getBillingAddress();

            $em->detach($billingAddress);

            $shippingAddress = $billingAddress;

            $form = $this->createForm(ShippingAddressType::class, $shippingAddress);
            if ($order->getShippingMessage()) {
                $form->get('shippingMessage')->setData($order->getShippingMessage());
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
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

            return $this->render('@DyweeOrderCMSBundle/Shipping/shipping.html.twig', [
                'home' => $form->createView()
            ]);
        }

        $this->addFlash('warning', 'votre session a expirée');

        return $this->redirectToRoute('basket_view');
    }


    /**
     * @Route(name="checkout_shipping_third_person", path="checkout/shipping/third")
     */
    public function thirdPersonShippingAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        $em = $this->getDoctrine()->getManager();
        $smr = $em->getRepository(ShippingMethod::class);

        $shippingMethods = $smr->myfindBy($order->getShippingAddress()->getCity()->getCountry(), $order->getWeight());

        $liv24R = false;
        $livHOM = false;

        /** @var ShippingMethod $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            if ($shippingMethod->getType() === '24R') {
                $liv24R = true;
                $this->get('session')->set('24RMethod', $shippingMethod);
            } elseif ($shippingMethod->getType() === 'HOM') {
                $livHOM = true;
                $this->get('session')->set('HomMethod', $shippingMethod);
            }
        }

        // HOM FORM
        if ($livHOM) {
            $shippingAddress = $order->getShippingAddress();

            $formHome = $this->get('form.factory')->create(new ShippingAddressType(), $shippingAddress);
            $formHome->remove('email')->add('email', 'repeated', [
                'type'            => 'email',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options'         => ['required' => true],
                'first_options'   => ['label' => 'Adresse e-mail'],
                'second_options'  => ['label' => 'Confirmer Adresse e-mail']
            ])
                ->add('other', 'text', ['required' => false]);

            if (isset($formHome) && $formHome->handleRequest($request)->isValid()) {
                $data = $formHome->getData();

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');

                return $this->step4Action($order, 'HomMethod');
            } else {
                $data['home'] = $formHome->createView();
            }
        }

        //24R FORM
        if ($liv24R) {
            $formMR = $this->createForm([])
                ->add('country', 'entity', [
                    'class'    => 'DyweeAddressBundle:Country',
                    'property' => 'name',
                    'required' => false,
                    'data'     => $shippingAddress->getCity()->getCountry()
                ])
                ->add('zip', 'text', ['required' => false, 'data' => $shippingAddress->getCity()->getZip()])
                ->add('ref', 'hidden')
                ->add('mrSave', 'submit')
                ->getForm();

            if ($formMR->handleRequest($request)->isValid()) {
                $cr = $em->getRepository('DyweeAddressBundle:Country');
                $data = $formMR->getData();

                $country = $cr->findOneById($data['country']->getId());

                $shippingAddress->setCountry($country);

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('24R');
                $order->setDeliveryInfo($data['ref']);

                return $this->step4Action($order, '24RMethod');
            } else {
                $data['mr'] = $formMR->createView();
            }
        }
        $data['step'] = 3;

        return $this->render('@DyweeOrderBundle/Basket/shipping.html.twig', $data);
    }

    /**
     * @Route(name="checkout_shipping_method", path="checkout/shipping/method")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function shippingMethodAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm(ShippingOptionsType::class, [])
            ->add('validate', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shippingMethodRepository = $em->getRepository(ShippingMethod::class);
            $shippingMethod = $shippingMethodRepository->find($form->getData()['shippingMethod']);

            $order->setShippingMethod($shippingMethod);
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('checkout_overview'));
        }

        if (count($this->get('dywee_order.shipment_method')->calculateForOrder($order)) === 1) {
            $this->autoSelectShippingOption();

            return $this->redirect($this->generateUrl('checkout_overview'));
        }

        $checkoutStatEvent = new CheckoutStatEvent($order, $this->getUser(), DyweeOrderCMSEvent::DISPLAY_SHIPPING_METHODS);

        $this->get('event_dispatcher')->dispatch($checkoutStatEvent, DyweeOrderCMSEvent::DISPLAY_SHIPPING_METHODS);

        return $this->render('@DyweeOrderCMSBundle/Shipping/shipping_methods.html.twig', [
            'order' => $order,
            'form'  => $form->createView()
        ]);
    }

    private function autoSelectShippingOption()
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        $em = $this->getDoctrine()->getManager();

        $shippingMethods = $this->get('dywee_order.shipment_method')->calculateForOrder($order);

        if (is_array($shippingMethods) && count($shippingMethods) !== 1) {
            throw new \LogicException('Cannot auto select shipping method if there is no shipping method or if there are more than 1');
        }

        $shippingMethodRepository = $em->getRepository(ShippingMethod::class);
        $shippingMethod = $shippingMethodRepository->find(array_values($shippingMethods)[0]);

        $order->setShippingMethod($shippingMethod);

        $em->persist($order);
        $em->flush();
    }

    /**
     * @Route(name="checkout_overview", path="checkout/overview")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function overviewAction()
    {
        $order = $this->get('dywee_order_cms.basket_manager')->getBasket();

        if ($this->get('dywee_order_cms.order_session_handler')->tryToAddCustomer($order)) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }


        if ($order->countProducts() > 0) {
            $data = ['order' => $order];

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

            $this->get('event_dispatcher')->dispatch($checkoutStatEvent, DyweeOrderCMSEvent::DISPLAY_RECAP);

            return $this->render('@DyweeOrderCMSBundle/Checkout/recap.html.twig', $data);
        } else {
            $this->addFlash('warning', 'votre session a expirée');

            return $this->redirectToRoute('basket_view');
        }
    }

    /**
     * @Route(path="checkout/confirmation", name="checkout_confirmation")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function confirmationAction(Request $request)
    {
        $order = $this->getDoctrine()->getRepository(BaseOrder::class)->find($request->getSession()->get('validatedOrderId'));

        if (!in_array($order->getPaymentStatus(), [BaseOrderInterface::PAYMENT_VALIDATED, BaseOrderInterface::PAYMENT_AUTHORIZED])) {
            return $this->redirectToRoute('checkout_fail');
        }

        return $this->render('@DyweeOrderCMSBundle/Checkout/success.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route(path="checkout/fail", name="checkout_fail")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function failedAction(Request $request)
    {
        return new Response('Failed to succeed the payment');
    }

    /**
     * @Route(name="paypal_checkout", path="paypal/checkout")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    /*
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
    /*
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
    */
}
