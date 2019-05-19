<?php

namespace Dywee\OrderCMSBundle\Listener;

use Dywee\CMSBundle\DyweeCMSEvent;
use Dywee\CMSBundle\Entity\Page;
use Dywee\CMSBundle\Event\PageElementModalBuilderEvent;
use Dywee\OrderCMSBundle\Service\PageElementModalHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PageElementModalListener implements EventSubscriberInterface
{
    /** @var PageElementModalHandler  */
    private $pageElementModalHandler;

    /**
     * PageElementModalListener constructor.
     *
     * @param PageElementModalHandler $pageElementModalHandler
     */
    public function __construct(PageElementModalHandler $pageElementModalHandler)
    {
        $this->pageElementModalHandler = $pageElementModalHandler;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            DyweeCMSEvent::BUILD_ADMIN_PLUGIN_BOX          => ['addElementToPage', -5],
            DyweeCMSEvent::BUILD_HOMEPAGE_ADMIN_PLUGIN_BOX => ['addElementToHomepage', -5],
        ];
    }

    /**
     * @param PageElementModalBuilderEvent $pageElementModalBuidlerEvent
     */
    public function addElementToPage(PageElementModalBuilderEvent $pageElementModalBuidlerEvent)
    {
        $pageElementModalBuidlerEvent->addData($this->pageElementModalHandler->addPageElements(), Page::TYPE_NORMALPAGE);
    }

    /**
     * @param PageElementModalBuilderEvent $pageElementModalBuidlerEvent
     */
    public function addElementToHomepage(PageElementModalBuilderEvent $pageElementModalBuidlerEvent)
    {
        $pageElementModalBuidlerEvent->addData($this->pageElementModalHandler->addPageElements(), Page::TYPE_HOMEPAGE);
    }
}
