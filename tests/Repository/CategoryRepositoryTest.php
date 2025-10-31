<?php

namespace CampaignBundle\Tests\Repository;

use CampaignBundle\Entity\Category;
use CampaignBundle\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * 对应组件的测试类。
 * @internal
 */
#[CoversClass(CategoryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CategoryRepositoryTest extends AbstractRepositoryTestCase
{
    private CategoryRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CategoryRepository::class);
    }

    public function testFindAll(): void
    {
        $categories = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(0, count($categories));
    }

    public function testSave(): void
    {
        $category = $this->createCategory('新品促销', false, 5);

        $this->repository->save($category, true);

        $this->assertNotNull($category->getId());
        $this->assertNotEquals('0', $category->getId());

        $savedCategory = $this->repository->find($category->getId());
        $this->assertInstanceOf(Category::class, $savedCategory);
        $this->assertEquals('新品促销', $savedCategory->getTitle());
        $this->assertFalse($savedCategory->isValid());
        $this->assertEquals(5, $savedCategory->getSortNumber());
    }

    public function testRemove(): void
    {
        $category = $this->createCategory('季节活动', true, 10);
        self::getEntityManager()->persist($category);
        self::getEntityManager()->flush();

        $categoryId = $category->getId();
        $this->assertNotNull($this->repository->find($categoryId));

        $this->repository->remove($category, true);

        $this->assertNull($this->repository->find($categoryId));
    }

    private function createCategory(string $title, bool $valid, int $sortNumber): Category
    {
        $category = new Category();
        $category->setTitle($title);
        $category->setValid($valid);
        $category->setSortNumber($sortNumber);

        return $category;
    }

    /** @return CategoryRepository */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $category = new Category();
        $category->setTitle('Test Category ' . uniqid());
        $category->setValid(true);
        $category->setSortNumber(1);

        return $category;
    }
}
