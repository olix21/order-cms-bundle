<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class OrderCMSAdminDashboardHandler
{
    /** @var Router  */
    private $router;

    /**
     * OrderCMSAdminDashboardHandler constructor.
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
    public function getDashboardElement()
    {
        $elements = [
            'key'   => 'order_cms',
            'boxes' => [
                [
                    'column' => 'col-md-4',
                    'type'   => 'default',
                    'title'  => 'order.dashboard.stat',
                    'body'   => [
                        [
                            'boxBody'    => false,
                            'controller' => 'DyweeOrderCMSBundle:Dashboard:Stat'
                        ]
                    ]
                ]
            ],
        ];

        return $elements;
    }

    /**
     * @return array
     */
    public function getJs()
    {
        return [
            ['type' => 'file', 'url' => 'absolute', 'src' => 'https://www.google.com/jsapi'],
            ['type' => 'script', 'script' => 'var stats = [];
        stats.push([\'Mois\', \'panier\', \'facturation\', \'livraison\', \'recap\']);
        {% for stat in stats %}
            stats.push([
                \'{{ stat.createdAt }}\',
                {{ stat[\'dywee_order_cms.display_basket\']}},
                {{ stat[\'dywee_order_cms.display_billing\'] }},
                {{ stat[\'dywee_order_cms.valid_shipping\'] }},
                {{ stat[\'dywee_order_cms.display_recap\'] }}
                ]);'],
            ['type' => 'file', 'url' => 'relative', 'src' => 'bundles/dyweeordercms/js/stat.js'],
        ];
    }
}
