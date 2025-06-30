<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\RewardRepository;
use CampaignBundle\Tests\BaseTestCase;

class RewardRepositoryTest extends BaseTestCase
{
    private RewardRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(RewardRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(RewardRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $rewards = $this->repository->findAll();
        $this->assertIsArray($rewards);
    }
}