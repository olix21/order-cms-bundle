<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class MemberSidebarHandler
 *
 * @package Dywee\OrderCMSBundle\Service
 */
class MemberSidebarHandler
{
    /** @var RouterInterface  */
    private $router;

    /**
     * MemberSidebarHandler constructor.
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
        $menu = [
            'key'      => 'order',
            'icon'     => 'fa fa-shopping-cart',
            'label'    => 'order.sidebar.label',
            'route' => $this->router->generate('member_order_list')
        ];

        return $menu;
    }
}