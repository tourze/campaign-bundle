<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\ChanceRepository;
use CampaignBundle\Tests\BaseTestCase;

class ChanceRepositoryTest extends BaseTestCase
{
    private ChanceRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(ChanceRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(ChanceRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $chances = $this->repository->findAll();
        $this->assertIsArray($chances);
    }
}