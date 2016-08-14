<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 6/08/16
 * Time: 10:12
 */
namespace Dywee\OrderCMSBundle\Entity;
use Dywee\CoreBundle\Model\PersistableInterface;
use Dywee\OrderBundle\Entity\BaseOrderInterface;


interface OrderStatInterface extends PersistableInterface
{
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrderStat
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set type
     *
     * @param string $type
     *
     * @return OrderStat
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set trackingKey
     *
     * @param string $trackingKey
     *
     * @return OrderStat
     */
    public function setTrackingKey($trackingKey);

    /**
     * Get trackingKey
     *
     * @return string
     */
    public function getTrackingKey();

    /**
     * @param BaseOrderInterface $order
     * @return $this
     */
    public function setOrder(BaseOrderInterface $order);

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @param boolean $auth
     * @return $this
     */
    public function setAuthentified($auth);

    public function getAuthentified();

    /**
     * Set attempts
     *
     * @param integer $attempts
     *
     * @return OrderStat
     */
    public function setAttempts($attempts);

    /**
     * Get attempts
     *
     * @return integer
     */
    public function getAttempts();
}