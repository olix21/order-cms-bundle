<?php

namespace Dywee\OrderCMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;

/**
 * OrderStat
 *
 * @ORM\Table(name="order_stat")
 * @ORM\Entity(repositoryClass="Dywee\OrderCMSBundle\Repository\OrderStatRepository")
 */
class OrderStat implements OrderStatInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Dywee\OrderBundle\Entity\BaseOrder")
     */
    private $order;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=40)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="trackingKey", type="string", length=50)
     */
    private $trackingKey;

    /**
     * @ORM\Column(name="authentified", type="boolean")
     */
    private $authentified = false;

    /**
     * @ORM\Column(name="attempts", type="smallint")
     */
    private $attempts = 1;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingKey($trackingKey)
    {
        $this->trackingKey = $trackingKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingKey()
    {
        return $this->trackingKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(BaseOrderInterface $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthentified($auth)
    {
        $this->authentified = $auth;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthentified()
    {
        return $this->authentified;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttempts()
    {
        return $this->attempts;
    }
}
