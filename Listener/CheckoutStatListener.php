<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\OrderCMSBundle\Service\OrderStatManager;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;


class CheckoutStatListener implements EventSubscriberInterface
{
    /** @var OrderStatManager  */
    private $orderStatManager;

    /**
     * CheckoutStatListener constructor.
     *
     * @param OrderStatManager $orderStatManager
     */
    public function __construct(OrderStatManager $orderStatManager)
    {
        $this->orderStatManager = $orderStatManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeOrderCMSEvent::DISPLAY_RECAP            => ['handleStat'],
            DyweeOrderCMSEvent::DISPLAY_PAYMENT_METHOD   => ['handleStat'],
            DyweeOrderCMSEvent::DISPLAY_SHIPPING         => ['handleStat'],
            DyweeOrderCMSEvent::DISPLAY_SHIPPING_METHODS => ['handleStat'],
            DyweeOrderCMSEvent::REDIRECT_TO_PAYMENT      => ['handleStat'],
            DyweeOrderCMSEvent::VALID_PAYMENT            => ['handleStat'],
            DyweeOrderCMSEvent::VALID_PAYMENT_METHOD     => ['handleStat'],
            DyweeOrderCMSEvent::VALID_SHIPPING           => ['handleStat'],
            DyweeOrderCMSEvent::DISPLAY_BASKET           => ['handleStat'],
            //DyweeOrderCMSEvent::DISPLAY_BILLING => array('handleStat'),
            //DyweeOrderCMSEvent::VALID_BASKET => array('handleStat'),
            //DyweeOrderCMSEvent::VALID_BILLING => array('handleStat'),
        ];
    }

    /**
     * @param CheckoutStatEvent $checkoutStatEvent
     */
    public function handleStat(CheckoutStatEvent $checkoutStatEvent)
    {
        $this->orderStatManager->createStat($checkoutStatEvent->getOrder(), $checkoutStatEvent->getEvent(), $checkoutStatEvent->isAuthentified());
    }
}