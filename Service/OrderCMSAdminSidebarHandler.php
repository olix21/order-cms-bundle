<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\RouterInterface;

class OrderCMSAdminSidebarHandler
{
    /** @var RouterInterface  */
    private $router;

    /**
     * OrderCMSAdminSidebarHandler constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getSideBarMenuElement()
    {

       return [
            'key'      => 'order',
            'children' => [
                [
                    'icon'  => 'fa fa-area-chart',
                    'label' => 'Stat commandes',
                    'route' => $this->router->generate('order_cms_stat')
                ],
            ]
        ];
    }
}