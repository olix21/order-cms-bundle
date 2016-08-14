<?php

namespace Dywee\OrderCMSBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class SessionOrderHandler{
    private $session;
    private $em;

    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function getOrderFromSession()
    {
        $order = $this->session->get('order');

        $order = $this->em->getRepository('DyweeOrderBundle:BaseOrder')->findOneById($order->getId());

        if($order)
            return $order;

        return $this->newOrder();

    }

    public function newOrder($persist = true)
    {
        $order = new BaseOrder();
        $order->setState(BaseOrder::STATE_IN_SESSION);
        // TODO: rendre dynamique via les paramètres
        $order->setIsPriceTTC($this->isPriceTTC());

        if($persist)
        {
            $this->em->persist($order);
            $this->em->flush();
        }
        $this->session->set('order', $order);

        return $order;
    }

    // TODO: rendre dynamique via les paramètres
    public function isPriceTTC()
    {
        return true;
    }
}