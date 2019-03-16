<?php

namespace Dywee\OrderCMSBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStatManager
{
    /** @var SessionInterface */
    private $session;

    /**
     * SessionStatManager constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function setTrackingKey()
    {
        $key = time() . '_' . $_SERVER['REMOTE_ADDR'] . '_' . random_int(0, 99);
        $this->session->set('order_tracking_key', $key);

        return $key;
    }

    /**
     * @return mixed|string
     */
    public function getTrackingKey()
    {
        $trackingKey = $this->session->get('order_tracking_key');
        if (!$trackingKey)
            return $this->setTrackingKey();

        return $trackingKey;
    }

    /**
     *
     */
    public function removeTrackingKey()
    {
        $this->session->remove('order_tracking_key');
    }

}