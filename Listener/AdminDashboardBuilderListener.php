<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\CoreBundle\Event\AdminDashboardBuilderEvent;
use Dywee\OrderCMSBundle\Service\OrderCMSAdminDashboardHandler;
use Dywee\CoreBundle\DyweeCoreEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminDashboardBuilderListener implements EventSubscriberInterface{
    private $orderCMSAdminDashboardHandler;

    public function __construct(OrderCMSAdminDashboardHandler $orderCMSAdminDashboardHandler)
    {
        $this->orderCMSAdminDashboardHandler = $orderCMSAdminDashboardHandler;
    }


    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            DyweeCoreEvent::BUILD_ADMIN_DASHBOARD => array('addElementToDashboard', 2040)
        );
    }

    public function addElementToDashboard(AdminDashboardBuilderEvent $adminDashboardBuilderEvent)
    {
        $adminDashboardBuilderEvent->addAdminElement($this->orderCMSAdminDashboardHandler->getSideBarMenuElement());
        $adminDashboardBuilderEvent->addJs($this->orderCMSAdminDashboardHandler->getJs());
    }

}