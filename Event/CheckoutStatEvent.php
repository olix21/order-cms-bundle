<?php

namespace Dywee\OrderCMSBundle\Event;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class CheckoutStatEvent extends Event
{
    private $order;
    private $user;
    private $auth;
    private $event;

    public function __construct(BaseOrderInterface $order, User $user, $event, $auth = false)
    {
        $this->order = $order;
        $this->user = $user;
        $this->event = $event;
        $this->auth = $auth;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function isAuthentified()
    {
        return $this->auth;
    }

    public function setAuthentified($auth)
    {
        $this->auth = $auth;
        return $this;
    }
}