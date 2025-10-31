<?php

declare(strict_types=1);

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Chance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Chance>
 */
#[AsRepository(entityClass: Chance::class)]
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
            ->select('COUNT(a.id)')
            ->where('a.user = :user AND a.campaign = :campaign')
            ->setParameter('user', $user)
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return intval($res);
    }

    public function save(Chance $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chance $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
