<?php

namespace Dywee\OrderCMSBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dywee\AddressBundle\Entity\Country;
use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCountryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var   */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /*
        $order1 = new BaseOrder();

        $order2 = new BaseOrder();

        $order3 = new BaseOrder();

        $order4 = new BaseOrder();

        $manager->persist($order1);
        $manager->persist($order2);
        $manager->persist($order3);
        $manager->persist($order4);
        $manager->flush();
        */
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
