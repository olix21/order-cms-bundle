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

        $orderStat = $orderStatRepository->retrievedStatForOrder($order, $event, $trackingKey);

        if(count($orderStat) > 0)
        {
            $orderStat = $orderStat[0];
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

        $types = array(
            DyweeOrderCMSEvent::DISPLAY_BASKET,
            DyweeOrderCMSEvent::DISPLAY_BILLING,
            DyweeOrderCMSEvent::VALID_BILLING,
            DyweeOrderCMSEvent::DISPLAY_SHIPPING,
            DyweeOrderCMSEvent::VALID_SHIPPING,
            DyweeOrderCMSEvent::DISPLAY_RECAP
        );


        $rawStats = $osr->getStats($types, $beginAt, $endAt, $timeScale);

        $stats = array();

        $date = clone $beginAt;

        $diff = (int) $endAt->diff($beginAt)->format('%a');

        for($i = 0; $i < $diff; $i++)
        {
            $key = $date->modify('+1 day')->format('d/m/Y');
            $stats[$key] = array(
                'createdAt' => $key);

            foreach($types as $type)
                $stats[$key][$type] = 0;
        }

        foreach($rawStats as $stat)
            $stats[$stat['createdAt']->format('d/m/Y')][$stat['type']] = $stat['total'];

        return $stats;
    }

}