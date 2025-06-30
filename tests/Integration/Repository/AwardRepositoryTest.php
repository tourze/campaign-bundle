<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\AwardRepository;
use CampaignBundle\Tests\BaseTestCase;

class AwardRepositoryTest extends BaseTestCase
{
    private AwardRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(AwardRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(AwardRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $awards = $this->repository->findAll();
        $this->assertIsArray($awards);
    }
}