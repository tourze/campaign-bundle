<?php

declare(strict_types=1);

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Limit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * Limit 实体的仓库类。
 *
 * @extends ServiceEntityRepository<Limit>
 */
#[AsRepository(entityClass: Limit::class)]
class LimitRepository extends ServiceEntityRepository
{
    /**
     * 构造新的 LimitRepository。
     *
     * @param ManagerRegistry $registry The registry manager
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Limit::class);
    }

    /**
     * 保存 Limit 实体。
     *
     * @param Limit   $entity The entity to save
     * @param bool $flush  Whether to flush the entity manager
     */
    public function save(Limit $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除 Limit 实体。
     *
     * @param Limit   $entity The entity to remove
     * @param bool $flush  Whether to flush the entity manager
     */
    public function remove(Limit $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
