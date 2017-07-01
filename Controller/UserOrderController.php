<?php

namespace Dywee\OrderCMSBundle\Controller;


use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserOrderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(path="admin/order", name="admin_order_list")
     * @Route(path="members/order", name="member_order_list")
     */
    public function listAction()
    {
        $orders = $this->getDoctrine()->getRepository(BaseOrder::class)->findByBillingUser($this->getUser());

        return $this->render('DyweeOrderCMSBundle:User:list.html.twig', ['orders' => $orders]);
    }

    /**
     * @param BaseOrder $order
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(path="admin/order/{id}", name="admin_order_view")
     * @Route(path="members/order/{id}", name="member_order_view")
     */
    public function viewAction(BaseOrder $order)
    {
        // TODO Voter
        return $this->render('DyweeOrderCMSBundle:User:view.html.twig', ['order' => $order]);
    }
}