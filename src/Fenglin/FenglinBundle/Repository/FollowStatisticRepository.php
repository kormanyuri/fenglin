<?php

namespace Fenglin\FenglinBundle\Repository;

use Fenglin\FenglinBundle\Entity\FollowStatistic;

/**
 * FollowStatisticRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FollowStatisticRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $shopper
     */
    public function up($shopper)
    {
        /**
         * @var \Fenglin\FenglinBundle\Entity\FollowStatistic $followStatistic
         */

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        $date = new \DateTime();

        $year  = $date->format('Y');
        $month = $date->format('m');
        $day   = $date->format('d');


        $shopperId = $shopper->getId();


        $qb = $this->_em->createQueryBuilder();
        $qb->select('f')
            ->from('FenglinFenglinBundle:FollowStatistic', 'f')
            ->join('f.shopper', 's')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('s.id', ':shopperId'),
                $qb->expr()->eq('YEAR(f.date)', ':year'),
                $qb->expr()->eq('MONTH(f.date)', ':month'),
                $qb->expr()->eq('DAY(f.date)', ':day')
            ))
            ->setParameter(':shopperId', $shopperId)
            ->setParameter(':year', $year)
            ->setParameter(':month', $month)
            ->setParameter(':day', $day);

        $result = $qb->getQuery()->getResult();

        if (!$result) {
            $followStatistic = new FollowStatistic();
            $followStatistic->setDate(new \DateTime());
            $followStatistic->setCountFollow(1);
            $followStatistic->setShopper($shopper);

            $this->_em->persist($followStatistic);
            $this->_em->flush();
        } else {
            $followStatistic = $result[0];
            $count = $followStatistic->getCountFollow();
            $count++;
            $followStatistic->setCountFollow($count);

            $this->_em->persist($followStatistic);
            $this->_em->flush();
        }
    }

    /**
     * @param $apikey
     * @return int
     */
    public function getCountFollow($apikey)
    {
        /**
         * @var \Fenglin\FenglinBundle\Entity\FollowStatistic $followStatistic
         */

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        $date = new \DateTime();
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        $qb = $this->_em->createQueryBuilder();
        $qb->select('f')
            ->from('FenglinFenglinBundle:FollowStatistic', 'f')
            ->join('f.shopper', 's')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('s.apiKey', ':apiKey'),
                $qb->expr()->eq('YEAR(f.date)', ':year'),
                $qb->expr()->eq('MONTH(f.date)', ':month'),
                $qb->expr()->eq('DAY(f.date)', ':day')
            ))
            ->setParameter(':apiKey', $apikey)
            ->setParameter(':year', $year)
            ->setParameter(':month', $month)
            ->setParameter(':day', $day);

        $result = $qb->getQuery()->getResult();

        if (count($result) > 0) {
            return $result[0]->getCountFollow();
        } else {
            return 0;
        }
    }
}
