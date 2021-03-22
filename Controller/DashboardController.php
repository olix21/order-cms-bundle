<?php

namespace Dywee\OrderCMSBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Dywee\ProductBundle\Entity\BaseProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DashboardController extends AbstractController
{
    public function statAction()
    {
        return $this->render('@DyweeOrderCMSBundle/Stat/overview.html.twig', array(
            'stats' => $this->get('dywee_order_cms.stat_manager')->getStatsForTimeRange(new \DateTime('last week'))
        ));
    }
}
