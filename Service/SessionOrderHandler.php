<?php

namespace Dywee\OrderCMSBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class SessionOrderHandler
{
    /** @var Session */
    private $session;

    /** @var EntityManager */
    private $em;

    /**
     * SessionOrderHandler constructor.
     *
     * @param EntityManager $em
     * @param Session       $session
     */
    public function __construct(EntityManager $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    /**
     * @return BaseOrder|mixed
     */
    public function getOrderFromSession()
    {
        $order = $this->session->get('order');

        if (!$order) {
            return $this->newOrder();
        }

        $order = $this->em->getRepository('DyweeOrderBundle:BaseOrder')->findOneById($order->getId());

        if ($order) {
            return $order;
        }

        return $this->newOrder();

    }

    /**
     * @param bool $persist
     *
     * @return BaseOrder
     */
    public function newOrder($persist = true)
    {
        $order = new BaseOrder();
        $order->setState(BaseOrderInterface::STATE_IN_SESSION);
        // TODO: rendre dynamique via les paramètres
        $order->setIsPriceTTC($this->isPriceTTC());

        if ($persist) {
            $this->em->persist($order);
            $this->em->flush();
        }
        $this->session->set('order', $order);

        return $order;
    }

    /**
     * @return bool
     */
    // TODO: rendre dynamique via les paramètres
    public function isPriceTTC()
    {
        return true;
    }
}