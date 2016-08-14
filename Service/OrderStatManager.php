<?php

namespace Dywee\OrderCMSBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderCMSBundle\Entity\OrderStat;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class OrderStatManager{

    private $em;
    private $sessionManager;

    public function __construct(EntityManager $entityManager, SessionStatManager $sessionManager)
    {
        $this->em = $entityManager;
        $this->sessionManager = $sessionManager;
    }

    public function createStat(BaseOrderInterface $order, $event, $auth = false)
    {
        $orderStatRepository = $this->em->getRepository('DyweeOrderCMSBundle:OrderStat');

        $trackingKey = $this->sessionManager->getTrackingKey();

        $orderStat = $orderStatRepository->findOneBy(array(
            'order' => $order,
            'type'  => $event,
            'trackingKey' => $trackingKey
        ));

        if($orderStat)
        {
            $orderStat->setAttempts($orderStat->getAttempts() + 1);
        }
        else
        {
            $orderStat = new OrderStat();
            $orderStat->setOrder($order);
            $orderStat->setType($event);
            $orderStat->setAuthentified($auth);
            $orderStat->setTrackingKey($trackingKey);
        }

        $this->em->persist($orderStat);
        $this->em->flush();

        if($event == DyweeOrderCMSEvent::VALID_PAYMENT){
            $this->sessionManager->removeTrackingKey();
        }
    }

    public function getStatsForTimeRange($beginAt = null, $endAt = null, $timeScale = 'day')
    {
        if(!$beginAt)
            $beginAt = new \DateTime('last month');
        if(!$endAt)
            $endAt = new \DateTime();

        $osr = $this->em->getRepository('DyweeOrderCMSBundle:OrderStat');

        $displayBaskets = $osr->getStats(DyweeOrderCMSEvent::DISPLAY_BASKET, $beginAt, $endAt, $timeScale);
        $displayBillings = $osr->getStats(DyweeOrderCMSEvent::DISPLAY_SHIPPING, $beginAt, $endAt, $timeScale);
        $validBillings = $osr->getStats(DyweeOrderCMSEvent::VALID_SHIPPING, $beginAt, $endAt, $timeScale);
        $displayShippings = $osr->getStats(DyweeOrderCMSEvent::DISPLAY_SHIPPING, $beginAt, $endAt, $timeScale);
        $validShippings = $osr->getStats(DyweeOrderCMSEvent::VALID_SHIPPING, $beginAt, $endAt, $timeScale);
        //$shippingMethods = $osr->getStats(DyweeOrderCMSEvent::DIS, $beginAt, $endAt, $timeScale);
        $recaps = $osr->getStats(DyweeOrderCMSEvent::DISPLAY_RECAP, $beginAt, $endAt, $timeScale);

        $stats = array();

        $date = clone $beginAt;

        $diff = (int) $endAt->diff($beginAt)->format('%a');

        for($i = 0; $i < $diff; $i++)
        {
            $key = $date->modify('+1 day')->format('d/m/Y');
            $stats[$key] = array(
                'createdAt' => $key,
                'displayBaskets' => 0,
                'validBaskets' => 0,
                'displayBillings' => 0,
                'validBillings' => 0,
                'displayShippings' => 0,
                'validShippings' => 0,
                'recaps' => 0,
            );
        }

        //On organise les données des stats
        foreach($displayBaskets as $displayBasket)
            $stats[$displayBasket['createdAt']->format('d/m/Y')]['displayBaskets'] = $displayBasket['total'];

        foreach($displayBillings as $displayBilling)
            $stats[$displayBilling['createdAt']->format('d/m/Y')]['displayBillings'] = $displayBilling['total'];

        foreach($validBillings as $validBilling)
            $stats[$validBilling['createdAt']->format('d/m/Y')]['validBillings'] = $validBilling['total'];

        foreach($displayShippings as $displayShipping)
            $stats[$displayShipping['createdAt']->format('d/m/Y')]['displayShippings'] = $displayShipping['total'];

        foreach($validShippings as $validShipping)
            $stats[$validShipping['createdAt']->format('d/m/Y')]['validShippings'] = $validShipping['total'];

        foreach($recaps as $recap)
            $stats[$recap['createdAt']->format('d/m/Y')]['recaps'] = $recap['total'];

        return $stats;
    }

}