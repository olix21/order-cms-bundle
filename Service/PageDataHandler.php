<?php

namespace Dywee\OrderCMSBundle\Service;

class PageDataHandler
{
    /** @var SessionOrderHandler  */
    private $orderSessionManager;

    /**
     * PageDataHandler constructor.
     *
     * @param SessionOrderHandler $sessionManager
     */
    public function __construct(SessionOrderHandler $sessionManager)
    {
        $this->orderSessionManager = $sessionManager;
    }

    /**
     * @return array
     */
    public function addDataToPage()
    {
        return ['order' => $this->orderSessionManager->getOrderFromSession()];
    }

    /**
     * @return array
     */
    public function addDataToHomepage()
    {
        return $this->addDataToPage();
    }

    /**
     * @return array
     */
    public function addDataToNavbar()
    {
        return ['order' => $this->orderSessionManager->getOrderFromSession()];
    }

    /**
     * @return array
     */
    public function addDataToFooter()
    {
        return ['order' => $this->orderSessionManager->getOrderFromSession()];
    }
}
