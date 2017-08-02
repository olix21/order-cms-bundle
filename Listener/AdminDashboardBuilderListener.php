<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\CoreBundle\Event\DashboardBuilderEvent;
use Dywee\OrderCMSBundle\Service\OrderCMSAdminDashboardHandler;
use Dywee\CoreBundle\DyweeCoreEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminDashboardBuilderListener implements EventSubscriberInterface
{
    private $orderCMSAdminDashboardHandler;

    public function __construct(OrderCMSAdminDashboardHandler $orderCMSAdminDashboardHandler)
    {
        $this->orderCMSAdminDashboardHandler = $orderCMSAdminDashboardHandler;
    }


    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCoreEvent::BUILD_ADMIN_DASHBOARD => ['addElementToDashboard', 2040]
        ];
    }

    public function addElementToDashboard(DashboardBuilderEvent $adminDashboardBuilderEvent)
    {
        $adminDashboardBuilderEvent->addElement($this->orderCMSAdminDashboardHandler->getDashboardElement());
        $adminDashboardBuilderEvent->addJs($this->orderCMSAdminDashboardHandler->getJs());
    }

}