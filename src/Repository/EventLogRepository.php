<?php

namespace CampaignBundle\Repository;

use CampaignBundle\Entity\EventLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method EventLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventLog[]    findAll()
 * @method EventLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventLog::class);
    }
}
