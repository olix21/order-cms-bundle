<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class OrderCMSMemberSidebarHandler
{
    /** @var Router  */
    private $router;

    /**
     * OrderCMSMemberSidebarHandler constructor.
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
        $menu = array(
            'key' => 'order',
            'children' => array(
                array(
                    'icon' => 'fa fa-area-chart',
                    'label' => 'Stat commandes',
                    'route' => $this->router->generate('order_cms_stat')
                ),
            )
        );

        return $menu;
    }
}