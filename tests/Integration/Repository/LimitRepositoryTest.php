<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\LimitRepository;
use CampaignBundle\Tests\BaseTestCase;

class LimitRepositoryTest extends BaseTestCase
{
    private LimitRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(LimitRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(LimitRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $limits = $this->repository->findAll();
        $this->assertIsArray($limits);
    }
}