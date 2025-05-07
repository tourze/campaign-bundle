<?php

namespace CampaignBundle\ExpressionLanguage\Function;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Repository\ChanceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AutoconfigureTag('ecol.function.provider')]
class ChanceFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('countTotalChanceByCampaignAndUser', fn (...$args) => sprintf('\%s(%s)', 'countTotalChanceByCampaignAndUser', implode(', ', $args)), function ($values, ...$args) {
                $this->logger->debug('countTotalChanceByCampaignAndUser', [
                    'values' => $values,
                    'args' => $args,
                ]);

                return $this->countTotalChanceByCampaignAndUser($values, ...$args);
            }),
        ];
    }

    /**
     * 根据活动和用户来计算活动机会次数
     *
     * @param array $values 这里代表的是执行时的上下文信息，具体可以看 \AppBundle\ExpressionLanguage\MessageListener
     */
    public function countTotalChanceByCampaignAndUser(array $values, Campaign $campaign, UserInterface $user): int
    {
        return $this->chanceRepository->countTotalChanceByCampaignAndUser($campaign, $user);
    }
}
