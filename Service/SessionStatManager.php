<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionStatManager{

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setTrackingKey()
    {
        $key = time().'_'.$_SERVER['REMOTE_ADDR'].'_'.rand(0, 99);
        $this->session->set('order_tracking_key', $key);
        return $key;
    }

    public function getTrackingKey()
    {
        $trackingKey = $this->session->get('order_tracking_key');
        if(!$trackingKey)
            return $this->setTrackingKey();
        return $trackingKey;
    }

    public function removeTrackingKey()
    {
        $this->session->remove('order_tracking_key');
    }

}