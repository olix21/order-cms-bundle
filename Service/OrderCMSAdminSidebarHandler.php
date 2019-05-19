<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class OrderCMSAdminSidebarHandler
{
    /** @var Router  */
    private $router;

    /**
     * OrderCMSAdminSidebarHandler constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
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
