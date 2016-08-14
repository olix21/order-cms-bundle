<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class OrderCMSAdminSidebarHandler{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getSideBarMenuElement()
    {
        $menu = array(
            'key' => 'order',
            'children' => array(
                array(
                    'icon' => 'fa fa-cogs',
                    'label' => 'Stat commandes',
                    'route' => $this->router->generate('order_cms_stat')
                ),
            )
        );

        return $menu;
    }
}