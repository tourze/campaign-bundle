<?php

namespace CampaignBundle\Tests\Integration\Repository;

use CampaignBundle\Repository\EventLogRepository;
use CampaignBundle\Tests\BaseTestCase;

class EventLogRepositoryTest extends BaseTestCase
{
    private EventLogRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(EventLogRepository::class);
    }

    public function testRepository(): void
    {
        $this->assertInstanceOf(EventLogRepository::class, $this->repository);
    }

    public function testFindAll(): void
    {
        $eventLogs = $this->repository->findAll();
        $this->assertIsArray($eventLogs);
    }
}