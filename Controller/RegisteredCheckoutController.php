<?php
namespace Dywee\OrderCMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class RegisteredCheckoutController extends Controller
{
    public function registeredBillingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $addressRepository = $em->getRepository('DyweeAddressBundle:Address');
        $addresses = $addressRepository->findAddressForUser($this->getUser());

        if(count($addresses) == 0)
            return $this->render('DyweeOrderCMSBundle:Billing:no_registered_address.html.twig');
        else return $this->render('DyweeOrderCMSBundle:Billing:address_picker.html.twig', array('addresses' => $addresses));
    }

}
