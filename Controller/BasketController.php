<?php

namespace Dywee\OrderCMSBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderCMSBundle\DyweeOrderCMSEvent;
use Dywee\OrderCMSBundle\Event\CheckoutStatEvent;
use Dywee\ProductBundle\Entity\BaseProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class BasketController extends Controller
{
    /**
     * @Route(name="basket_view", path="basket")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function viewAction(Request $request)
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        if ($order) {

            if ($request->getSession()->get('bypassBasketEvents')) {
                $request->getSession()->set('bypassBasketEvents', false);
            } else {
                $this->get('dywee_order_cms.stat_manager')->createStat($order, DyweeOrderCMSEvent::DISPLAY_BASKET);
            }
        }

        return $this->render('DyweeOrderCMSBundle:Basket:basket.html.twig', ['order' => $order]);
    }

    /**
     * @Route(name="basket_sidebar", path="basket_nav_side")
     *
     * @return Response
     */
    public function navSideAction()
    {
        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        return $this->render('DyweeOrderCMSBundle:Basket:inMenu.html.twig', ['order' => $order]);
    }


    /**
     * @Route(name="basket_remove_product", path="basket/remove/{id}/{quantity}", defaults={"quantity"=-1}, options={"expose"=true})
     *
     * @param BaseProduct $product
     * @param int         $quantity
     * @param             $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|JsonResponse
     */
    public function removeAction(BaseProduct $product, $quantity, Request $request)
    {
        $basketManager = $this->get('dywee_order_cms.basket_manager');
        $basketManager->removeProduct($product, $quantity);

        $this->getDoctrine()->getManager()->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['type' => 'success', 'data' => ['quantity' => $basketManager->countProductQuantity($product)]]);
        } else {
            $request->getSession()->set('bypassBasketEvents', true);

            return $this->redirect($this->generateUrl('basket_view'));
        }
    }

    /**
     * @Route(name="basket_add_product", path="basket/add/{id}/{quantity}", defaults={"quantity"=1}, options={"expose"=true})
     * @Route(name="add_to_cart", path="basket/add/{id}/{quantity}", defaults={"quantity"=1}, options={"expose"=true})
     *
     * @param BaseProduct $product
     * @param int         $quantity
     * @param             $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|JsonResponse
     */
    public function addAction(BaseProduct $product, $quantity = 1, Request $request)
    {
        $basketManager = $this->get('dywee_order_cms.basket_manager');
        $basketManager->addProduct($product, $quantity);

        $this->getDoctrine()->getManager()->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['type' => 'success', 'data' => ['quantity' => $basketManager->countProductQuantity($product)]]);
        }

        $request->getSession()->set('bypassBasketEvents', true);

        return $this->redirect($this->generateUrl('basket_view'));
    }

    /**
     * @param BaseProduct $product
     * @param Request     $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route(name="basket_delete_product", path="basket/delete/{id}")
     *
     * TODO implement ajax handler
     */
    public function deleteAction(BaseProduct $product, Request $request)
    {
        $basketManager = $this->get('dywee_order_cms.basket_manager');
        $basketManager->removeProduct($product);

        $this->getDoctrine()->getManager()->flush();

        $request->getSession()->set('bypassBasketEvents', true);

        return $this->redirect($this->generateUrl('basket_view', ['disableEvents' => true]));
    }
}
