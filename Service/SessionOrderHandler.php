<?php

namespace Dywee\OrderCMSBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dywee\CoreBundle\Model\CustomerInterface;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class SessionOrderHandler
{
    private SessionInterface $session;
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(
        EntityManagerInterface $em, 
        SessionInterface $session, 
        Security $security
    ) {
        $this->em = $em;
        $this->session = $session;
        $this->security = $security;
    }

    /**
     * @return BaseOrder|mixed
     */
    public function getOrderFromSession()
    {
        $order = $this->session->get('order');

        if ($order) {
            // TODO is it really needed?
            $order = $this->em->getRepository(BaseOrder::class)->find($order->getId());

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
        $order->setStatus(BaseOrderInterface::STATE_IN_SESSION);
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
        if ($this->security->getUser() instanceof CustomerInterface) {
            $baseOrder->setCustomer($this->security->getUser());

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
