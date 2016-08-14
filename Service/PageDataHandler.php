<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class PageDataHandler
{
    private $orderSessionManager;

    public function __construct(SessionOrderHandler $sessionManager)
    {
        $this->orderSessionManager = $sessionManager;
    }

    public function addDataToPage()
    {
        return array('order' => $this->orderSessionManager->getOrderFromSession());
    }

    public function addDataToHomepage()
    {
        $return = $this->addDataToPage();
        return $return;
    }

    public function addDataToNavbar()
    {
        return array('order' => $this->orderSessionManager->getOrderFromSession());
    }

    public function addDataToFooter()
    {
        return array('order' => $this->orderSessionManager->getOrderFromSession());
    }

}
