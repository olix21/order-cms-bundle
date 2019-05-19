<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 9/08/17
 * Time: 18:02
 */

namespace Dywee\OrderCMSBundle\Controller;


use Dywee\OrderBundle\Entity\BaseOrder;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    /**
     * @Get(name="user_invoice_view", path="order/{id}/invoice")
     *
     * @param BaseOrder $order
     *
     * @return Response
     */
    public function vewAction(BaseOrder $order)
    {
        //TODO voters

        return $this->render('DyweeOrderCMSBundle:Order:invoice.html.twig', [
            'order' => $order
        ]);
    }
}
