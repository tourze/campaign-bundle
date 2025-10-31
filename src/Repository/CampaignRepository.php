<?php

declare(strict_types=1);

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * 营销活动实体的仓储类。
 *
 * @extends ServiceEntityRepository<Campaign>
 */
#[AsRepository(entityClass: Campaign::class)]
class CampaignRepository extends ServiceEntityRepository
{
    /**
     * 构造函数。
     *
     * @param ManagerRegistry $registry 管理注册表
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    /**
     * 保存营销活动实体。
     *
     * @param Campaign $entity 营销活动实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function save(Campaign $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 移除营销活动实体。
     *
     * @param Campaign $entity 营销活动实体
     * @param bool $flush 是否立即刷新到数据库
     */
    public function remove(Campaign $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
