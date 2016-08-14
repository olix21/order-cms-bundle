<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\OrderCMSBundle\Service\OrderStatManager;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;


class CheckoutStatListener implements EventSubscriberInterface{

    private $orderStatManager;

    public function __construct(OrderStatManager $orderStatManager)
    {
        $this->orderStatManager = $orderStatManager;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            DyweeOrderCMSEvent::DISPLAY_RECAP => array('handleStat'),
            DyweeOrderCMSEvent::DISPLAY_PAYMENT_METHOD=> array('handleStat'),
            DyweeOrderCMSEvent::DISPLAY_SHIPPING => array('handleStat'),
            DyweeOrderCMSEvent::REDIRECT_TO_PAYMENT => array('handleStat'),
            DyweeOrderCMSEvent::VALID_PAYMENT => array('handleStat'),
            DyweeOrderCMSEvent::VALID_PAYMENT_METHOD => array('handleStat'),
            DyweeOrderCMSEvent::VALID_SHIPPING => array('handleStat'),
            //DyweeOrderCMSEvent::DISPLAY_BASKET => array('handleStat'),
            //DyweeOrderCMSEvent::DISPLAY_BILLING => array('handleStat'),
            //DyweeOrderCMSEvent::VALID_BASKET => array('handleStat'),
            //DyweeOrderCMSEvent::VALID_BILLING => array('handleStat'),
        );
    }

    public function handleStat(CheckoutStatEvent $checkoutStatEvent)
    {
        $this->orderStatManager->createStat($checkoutStatEvent->getOrder(), $checkoutStatEvent->getEvent(), $checkoutStatEvent->isAuthentified());
    }
}