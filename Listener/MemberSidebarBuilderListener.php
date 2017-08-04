<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\CoreBundle\DyweeCoreEvent;
use Dywee\CoreBundle\Event\SidebarBuilderEvent;
use Dywee\OrderCMSBundle\Service\MemberSidebarHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class MemberSidebarBuilderListener implements EventSubscriberInterface
{
    /** @var MemberSidebarHandler $memberSidebarHandler  */
    private $memberSidebarHandler ;

    /** @var bool */
    private $inSidebar;

    /**
     * AdminSidebarBuilderListener constructor.
     *
     * @param MemberSidebarHandler $memberSidebarHandler
     * @param bool                $inSidebar
     */
    public function __construct(MemberSidebarHandler $memberSidebarHandler, bool $inSidebar)
    {
        $this->memberSidebarHandler = $memberSidebarHandler;
        // TODO
        $this->inSidebar = true;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCoreEvent::BUILD_MEMBER_SIDEBAR => ['addElementToSidebar', -10],
        ];
    }

    /**
     * @param SidebarBuilderEvent $sidebarBuilderEvent
     */
    public function addElementToSidebar(SidebarBuilderEvent $sidebarBuilderEvent)
    {
        if($this->inSidebar) {
            $sidebarBuilderEvent->addElement($this->memberSidebarHandler ->getSideBarMenuElement());
        }
    }

    /**
     * @return bool
     */
    public function isInSidebar() : bool
    {
        return $this->inSidebar;
    }

}