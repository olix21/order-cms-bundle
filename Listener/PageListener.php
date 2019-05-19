<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\CMSBundle\DyweeCMSEvent;
use Dywee\CMSBundle\Event\FooterBuilderEvent;
use Dywee\CMSBundle\Event\HomepageBuilderEvent;
use Dywee\CMSBundle\Event\NavbarBuilderEvent;
use Dywee\CMSBundle\Event\PageBuilderEvent;
use Dywee\OrderCMSBundle\Service\PageDataHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PageListener implements EventSubscriberInterface
{
    /** @var PageDataHandler  */
    private $pageDataHandler;

    /**
     * PageListener constructor.
     *
     * @param PageDataHandler $pageDataHandler
     */
    public function __construct(PageDataHandler $pageDataHandler)
    {
        $this->pageDataHandler = $pageDataHandler;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCMSEvent::BUILD_PAGE     => ['addElementToPage'],
            DyweeCMSEvent::BUILD_HOMEPAGE => ['addElementToHomepage'],
            DyweeCMSEvent::BUILD_NAVBAR   => ['addElementToNavbar'],
            DyweeCMSEvent::BUILD_FOOTER   => ['addElementToFooter'],
        ];
    }

    /**
     * @param PageBuilderEvent $pageBuilderEvent
     */
    public function addElementToPage(PageBuilderEvent $pageBuilderEvent)
    {
        $pageBuilderEvent->addData($this->pageDataHandler->addDataToPage());
    }

    /**
     * @param HomepageBuilderEvent $homepageBuilderEvent
     */
    public function addElementToHomepage(HomepageBuilderEvent $homepageBuilderEvent)
    {
        $homepageBuilderEvent->addData($this->pageDataHandler->addDataToHomepage());
    }

    /**
     * @param NavbarBuilderEvent $navbarBuilderEvent
     */
    public function addElementToNavbar(NavbarBuilderEvent $navbarBuilderEvent)
    {
        $navbarBuilderEvent->addData($this->pageDataHandler->addDataToNavbar());
    }

    /**
     * @param FooterBuilderEvent $footerBuilderEvent
     */
    public function addElementToFooter(FooterBuilderEvent $footerBuilderEvent)
    {
        $footerBuilderEvent->addData($this->pageDataHandler->addDataToFooter());
    }
}
