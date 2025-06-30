<?php

namespace CampaignBundle\Command;

use CampaignBundle\Entity\Chance;
use CampaignBundle\Repository\ChanceRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '检查用户的机会并实时过期处理')]
class ChanceExpireCommand extends Command
{
    public const NAME = 'campaign:chance-expire';
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $chances = $this->chanceRepository->createQueryBuilder('a')
            ->where('a.valid = true AND a.expireTime <= :now')
            ->setParameter('now', CarbonImmutable::now())
            ->getQuery()
            ->toIterable();
        foreach ($chances as $chance) {
            /* @var Chance $chance */
            $chance->setRemark(__METHOD__ . '过期处理' . CarbonImmutable::now()->toString());
            $chance->setValid(false);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
