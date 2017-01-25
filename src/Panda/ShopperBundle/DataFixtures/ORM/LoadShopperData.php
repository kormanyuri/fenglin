<?php
/**
 * Created by PhpStorm.
 * User: korman
 * Date: 23.01.17
 * Time: 14:17
 */

namespace Panda\ShopperBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Panda\ShopperBundle\Entity\Shopper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadShopperData
 * @package Panda\ShopperBundle\DataFixtures\ORM
 */
class LoadShopperData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $this->loadShopper($manager);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadShopper(ObjectManager $manager)
    {
//        $encoder = $this->container->get('security.password_encoder');


        $shopper = new Shopper();
//        $password = $encoder->encodePassword($shopper, '1demo!');

        $shopper->setEmail('shopper' . rand(0, 1000) . '@test.com');
        $shopper->setPassword('');
        $shopper->setName('Shopper ' . rand(1, 200000));
        $shopper->setLogo('fixture_resources/shopper/logo/1.png');
        $shopper->setAddress('C1070AAM Capital Federal, Piedras No 623, Piso2 Dto.4');
        $shopper->setTel('+19001777777');
        $shopper->setTotalAmount(100);
        $shopper->setRebate(1.5);
        $shopper->setRebateLevelRate(2);
        $shopper->setRebateLevel2Rate(1.5);
        $shopper->setRebateLevel3Rate(1);
        $shopper->setStatus(Shopper::STATUS_ACTIVE);
        $shopper->setRole('ROLE_SHOPPER');
        $manager->persist($shopper);
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}