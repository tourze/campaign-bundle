<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\CampaignRepository;
use CampaignBundle\Tests\BaseTestCase;

class CampaignRepositoryTest extends BaseTestCase
{
    private CampaignRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(CampaignRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(CampaignRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $campaigns = $this->repository->findAll();
        $this->assertIsArray($campaigns);
    }
}