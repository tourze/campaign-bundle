<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\CategoryRepository;
use CampaignBundle\Tests\BaseTestCase;

class CategoryRepositoryTest extends BaseTestCase
{
    private CategoryRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(CategoryRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(CategoryRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $categories = $this->repository->findAll();
        $this->assertIsArray($categories);
    }
}