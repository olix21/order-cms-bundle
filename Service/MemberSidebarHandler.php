<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

/**
 * Class MemberSidebarHandler
 *
 * @package Dywee\OrderCMSBundle\Service
 */
class MemberSidebarHandler
{
    /** @var Router  */
    private $router;

    /**
     * MemberSidebarHandler constructor.
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
        $menu = [
            'key'      => 'order',
            'icon'     => 'fa fa-shopping-cart',
            'label'    => 'order.sidebar.label',
            'route' => $this->router->generate('member_order_list')
        ];

        return $menu;
    }
}
