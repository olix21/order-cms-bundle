<?php

namespace Dywee\OrderCMSBundle\Service;

class PageElementModalHandler
{
    /**
     * @return array
     */
    public function addPageElements()
    {
        return [
            [
                'key'            => 'product',
                'icon'           => 'beer',
                'value'          => 'Afficher un produit',
                'modalLabel'     => 'Afficher un produit qui prendra toute la taille de la page',
                'routeName'      => false,
                'routeForAdding' => false,
                'active'         => true
            ],
            [
                'key'            => 'productGallery',
                'icon'           => 'beer',
                'value'          => 'Gallerie de produit',
                'modalLabel'     => 'Choisissez une gallerie de produit à afficher sur la page',
                'routeName'      => false,//'cms_customForm_json',
                'routeForAdding' => false, //'cms_customForm_add',
                'active'         => true
            ],

        ];
    }
}