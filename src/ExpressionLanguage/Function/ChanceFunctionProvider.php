<?php

declare(strict_types=1);

namespace CampaignBundle\ExpressionLanguage\Function;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Repository\ChanceRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 提供机会相关的表达式语言函数。
 */
#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'ecol.function.provider')]
#[WithMonologChannel(channel: 'campaign')]
readonly class ChanceFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private ChanceRepository $chanceRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 获取表达式函数列表。
     *
     * @return array<ExpressionFunction>
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'countTotalChanceByCampaignAndUser',
                function (...$args): string {
                    $argStrings = [];
                    foreach ($args as $arg) {
                        $argStrings[] = is_scalar($arg) ? (string) $arg : 'arg';
                    }

                    return sprintf('\%s(%s)', 'countTotalChanceByCampaignAndUser', implode(', ', $argStrings));
                },
                function (array $values, Campaign $campaign, UserInterface $user): int {
                    $this->logger->debug('countTotalChanceByCampaignAndUser', [
                        'values' => $values,
                        'campaign' => $campaign,
                        'user' => $user,
                    ]);

                    /** @var array<string, mixed> $values */
                    return $this->countTotalChanceByCampaignAndUser($values, $campaign, $user);
                }
            ),
        ];
    }

    /**
     * 根据活动和用户来计算活动机会次数
     *
     * @param array<string, mixed> $values 这里代表的是执行时的上下文信息，具体可以看 \AppBundle\ExpressionLanguage\MessageListener
     */
    public function countTotalChanceByCampaignAndUser(array $values, Campaign $campaign, UserInterface $user): int
    {
        return $this->chanceRepository->countTotalChanceByCampaignAndUser($campaign, $user);
    }
}
