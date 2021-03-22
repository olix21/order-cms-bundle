<?php

namespace Dywee\OrderCMSBundle\Controller;

use Dywee\AddressBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class RegisteredCheckoutController extends AbstractController
{
    /**
     * @Route(name="address_picker_check", path="checkout/billing/address/{id}")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addressPickerAction(Request $request)
    {
        $addressRepository = $this->getDoctrine()->getRepository(Address::class);
        $addresses = $addressRepository->findByUser($this->getUser());

        if (count($addresses) > 0) {
            return $this->render('@DyweeOrderCMSBundle/Address/picker.html.twig', ['addresses' => $addresses]);
        }

        return $this->render('@DyweeOrderCMSBundle/Address/picker.html.twig', ['form' => $this->get('dywee_address.form_handler')->createForm()->createView(), 'addresses' => $addresses]);
    }
}
