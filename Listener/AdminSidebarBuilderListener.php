<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\OrderCMSBundle\Service\OrderCMSAdminSidebarHandler;
use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\AdminSidebarBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminSidebarBuilderListener implements EventSubscriberInterface{
    private $orderCMSAdminSidebarHandler;

    public function __construct(OrderCMSAdminSidebarHandler $orderCMSAdminSidebarHandler)
    {
        $this->orderCMSAdminSidebarHandler = $orderCMSAdminSidebarHandler;
    }


    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            DyweeCoreEvent::BUILD_ADMIN_SIDEBAR => array('addElementToSidebar', -15)
        );
    }

    public function addElementToSidebar(AdminSidebarBuilderEvent $adminSidebarBuilderEvent)
    {
        $adminSidebarBuilderEvent->addAdminElement($this->orderCMSAdminSidebarHandler->getSideBarMenuElement());
    }

}