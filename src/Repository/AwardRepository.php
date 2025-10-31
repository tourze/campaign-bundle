<?php

declare(strict_types=1);

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 权益奖励实体的仓储类。
 *
 * @extends ServiceEntityRepository<Award>
 */
#[AsRepository(entityClass: Award::class)]
class AwardRepository extends ServiceEntityRepository
{
    /**
     * 构造函数。
     *
     * @param ManagerRegistry $registry 管理注册表
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    /**
     * 保存权益奖励实体。
     *
     * @param Award $entity 权益奖励实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function save(Award $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 移除权益奖励实体。
     *
     * @param Award $entity 权益奖励实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function remove(Award $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
