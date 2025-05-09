<?php

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Chance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Chance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chance[]    findAll()
 * @method Chance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChanceRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chance::class);
    }

    /**
     * 根据活动和用户来计算活动机会次数
     */
    public function countTotalChanceByCampaignAndUser(Campaign $campaign, UserInterface $user): int
    {
        $res = $this->createQueryBuilder('a')
            ->where('a.user = :user AND a.campaign = :campaign')
            ->setParameter('user', $user)
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getSingleScalarResult();

        return intval($res);
    }
}
