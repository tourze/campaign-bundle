<?php

namespace CampaignBundle\Command;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Repository\CampaignRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '自动设置过期时间')]
class CheckExpiredCampaignCommand extends Command
{
    public const NAME = 'campaign:check-expired-campaign';
    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $qb = $this->campaignRepository
            ->createQueryBuilder('a')
            ->where('a.valid = true AND a.endTime <= :now')
            ->setParameter('now', CarbonImmutable::now())
        ;

        foreach ($qb->getQuery()->toIterable() as $item) {
            /* @var Campaign $item */
            $item->setValid(false);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
