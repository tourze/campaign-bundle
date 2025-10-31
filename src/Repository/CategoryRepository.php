<?php

declare(strict_types=1);

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * Category 实体的 Repository 类
 *
 * @extends ServiceEntityRepository<Category>
 */
#[AsRepository(entityClass: Category::class)]
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * 构造 CategoryRepository
     *
     * @param ManagerRegistry $registry 注册管理器
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * 保存 Category 实体
     *
     * @param Category $entity 要保存的实体
     * @param bool  $flush  是否刷新 EntityManager
     */
    public function save(Category $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除 Category 实体
     *
     * @param Category $entity 要删除的实体
     * @param bool  $flush  是否刷新 EntityManager
     */
    public function remove(Category $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
