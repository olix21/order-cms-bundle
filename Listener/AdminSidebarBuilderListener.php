<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\OrderCMSBundle\Service\OrderCMSAdminSidebarHandler;
use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\SidebarBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdminSidebarBuilderListener implements EventSubscriberInterface
{
    /** @var OrderCMSAdminSidebarHandler */
    private $orderCMSAdminSidebarHandler;

    /** @var bool */
    private $display;

    /**
     * AdminSidebarBuilderListener constructor.
     *
     * @param OrderCMSAdminSidebarHandler $orderCMSAdminSidebarHandler
     * @param                             $display
     */
    public function __construct(OrderCMSAdminSidebarHandler $orderCMSAdminSidebarHandler, $display)
    {
        $this->orderCMSAdminSidebarHandler = $orderCMSAdminSidebarHandler;
        $this->display = $display;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCoreEvent::BUILD_ADMIN_SIDEBAR => ['addElementToSidebar', -20]
        ];
    }

    /**
     * @param SidebarBuilderEvent $adminSidebarBuilderEvent
     */
    public function addElementToSidebar(SidebarBuilderEvent $adminSidebarBuilderEvent)
    {
        if ($this->display) {
            $adminSidebarBuilderEvent->addElement($this->orderCMSAdminSidebarHandler->getSideBarMenuElement());
        }
    }

    /**
     * @return bool
     */
    public function isInSidebar() : bool
    {
        return $this->display;
    }
}
