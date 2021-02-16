<?php

namespace Dywee\OrderCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class StatController extends AbstractController
{
    /**
     * @Route(name="order_cms_stat", path="admin/order/stat")
     */
    public function statAction()
    {
        return $this->render('DyweeOrderCMSBundle:Stat:dashboard.html.twig', array(
            'stats' => $this->get('dywee_order_cms.stat_manager')->getStatsForTimeRange()
        ));
    }
}
