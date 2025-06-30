<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\AttributeRepository;
use CampaignBundle\Tests\BaseTestCase;

class AttributeRepositoryTest extends BaseTestCase
{
    private AttributeRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(AttributeRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(AttributeRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $attributes = $this->repository->findAll();
        $this->assertIsArray($attributes);
    }
}