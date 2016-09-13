<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\Routing\Router;

class OrderCMSAdminDashboardHandler{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getDashboardElement()
    {
        $elements = array(
            'key' => 'order_cms',
            'boxes' => array(
                array(
                    'column' => 'col-md-4',
                    'type' => 'default',
                    'title' => 'order.dashboard.stat',
                    'body' => array(
                        array(
                            'boxBody' => false,
                            'controller' => 'DyweeOrderCMSBundle:Dashboard:Stat'
                        )
                    )
                )
            ),
        );

        return $elements;
    }

    public function getJs()
    {
        return array(
            array('type' => 'file', 'url' => 'absolute', 'src' => 'https://www.google.com/jsapi'),
            array('type' => 'script', 'script' => 'var stats = [];
        stats.push([\'Mois\', \'panier\', \'facturation\', \'livraison\', \'recap\']);
        {% for stat in stats %}
            stats.push([
                \'{{ stat.createdAt }}\',
                {{ stat[\'dywee_order_cms.display_basket\']}},
                {{ stat[\'dywee_order_cms.display_billing\'] }},
                {{ stat[\'dywee_order_cms.valid_shipping\'] }},
                {{ stat[\'dywee_order_cms.display_recap\'] }}
                ]);'),
            array('type' => 'file', 'url' => 'relative', 'src' => 'bundles/dyweeordercms/js/stat.js'),
        );
    }
}