<?php

namespace Dywee\OrderCMSBundle\Service;

use Doctrine\ORM\EntityManager;
use Dywee\CoreBundle\Model\CustomerInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SessionOrderHandler
{
    /** @var Session */
    private $session;

    /** @var EntityManager */
    private $em;

    /** @var TokenStorage */
    private $tokenStorage;

    /**
     * SessionOrderHandler constructor.
     *
     * @param EntityManager $em
     * @param Session       $session
     * @param TokenStorage  $tokenStorage
     */
    public function __construct(EntityManager $em, Session $session, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return BaseOrder|mixed
     */
    public function getOrderFromSession()
    {
        $order = $this->session->get('order');

        if ($order) {
            // TODO is it really needed?
            $order = $this->em->getRepository('DyweeOrderBundle:BaseOrder')->find($order->getId());

            if ($order) {
                return $order;
            }
        }

        $order = $this->newOrder();

        return $order;
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

        $this->tryToAddCustomer($order);

        if ($persist) {
            $this->em->persist($order);
            $this->em->flush($order);
        }
        $this->session->set('order', $order);

        return $order;
    }

    /**
     * @param BaseOrderInterface $baseOrder
     *
     * @return bool
     */
    public function tryToAddCustomer(BaseOrderInterface $baseOrder)
    {
        if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser() && $this->tokenStorage->getToken()->getUser() instanceof CustomerInterface) {
            $baseOrder->setCustomer($this->tokenStorage->getToken()->getUser());

            return true;
        }

        return false;
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