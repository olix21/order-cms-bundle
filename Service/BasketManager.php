<?php

namespace Dywee\OrderCMSBundle\Service;


use Dywee\CoreBundle\Model\ProductInterface;
use Dywee\OrderBundle\Service\OrderElementManager;

class BasketManager
{
    /** @var OrderElementManager  */
    private $orderElementManager;

    /** @var SessionOrderHandler  */
    private $sessionOrderHandler;

    /**
     * BasketManager constructor.
     *
     * @param OrderElementManager $orderElementManager
     * @param SessionOrderHandler $sessionOrderHandler
     */
    public function __construct(OrderElementManager $orderElementManager, SessionOrderHandler $sessionOrderHandler)
    {
        $this->orderElementManager = $orderElementManager;
        $this->sessionOrderHandler = $sessionOrderHandler;
    }

    /**
     * @param ProductInterface $product
     * @param int              $quantity
     */
    public function addProduct(ProductInterface $product, int $quantity)
    {
        $this->orderElementManager->addProduct($this->sessionOrderHandler->getOrderFromSession(), $product, $quantity);
    }

    /**
     * @param ProductInterface $product
     * @param int              $quantity
     */
    public function removeProduct(ProductInterface $product, int $quantity = 0)
    {
        $this->orderElementManager->removeProduct($this->sessionOrderHandler->getOrderFromSession(), $product, $quantity);
    }

    /**
     * @param ProductInterface $product
     * @param int              $quantity
     */
    public function updateProductQuantity(ProductInterface $product, int $quantity)
    {
        $this->orderElementManager->updateProductQuantity($this->sessionOrderHandler->getOrderFromSession(), $product, $quantity);
    }

    /**
     * @param ProductInterface $product
     */
    public function countProductQuantity(ProductInterface $product)
    {
        $this->orderElementManager->countProductQuantity($this->sessionOrderHandler->getOrderFromSession(), $product);
    }
}