<?php

declare(strict_types=1);

namespace CampaignBundle\Service;

use CampaignBundle\Entity\Attribute;
use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Category;
use CampaignBundle\Entity\Chance;
use CampaignBundle\Entity\EventLog;
use CampaignBundle\Entity\Limit;
use CampaignBundle\Entity\Reward;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private ?LinkGeneratorInterface $linkGenerator = null,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $this->linkGenerator) {
            return;
        }

        if (null === $item->getChild('通用活动')) {
            $item->addChild('通用活动');
        }

        $campaignMenu = $item->getChild('通用活动');
        if (null === $campaignMenu) {
            return;
        }

        $campaignMenu->addChild('活动分类')
            ->setUri($this->linkGenerator->getCurdListPage(Category::class))
            ->setAttribute('icon', 'fas fa-folder')
        ;

        $campaignMenu->addChild('活动管理')
            ->setUri($this->linkGenerator->getCurdListPage(Campaign::class))
            ->setAttribute('icon', 'fas fa-calendar-alt')
        ;

        $campaignMenu->addChild('活动属性')
            ->setUri($this->linkGenerator->getCurdListPage(Attribute::class))
            ->setAttribute('icon', 'fas fa-tags')
        ;

        $campaignMenu->addChild('奖励配置')
            ->setUri($this->linkGenerator->getCurdListPage(Award::class))
            ->setAttribute('icon', 'fas fa-gift')
        ;

        $campaignMenu->addChild('限制条件')
            ->setUri($this->linkGenerator->getCurdListPage(Limit::class))
            ->setAttribute('icon', 'fas fa-ban')
        ;

        $campaignMenu->addChild('参与机会')
            ->setUri($this->linkGenerator->getCurdListPage(Chance::class))
            ->setAttribute('icon', 'fas fa-ticket-alt')
        ;

        $campaignMenu->addChild('奖励记录')
            ->setUri($this->linkGenerator->getCurdListPage(Reward::class))
            ->setAttribute('icon', 'fas fa-trophy')
        ;

        $campaignMenu->addChild('参与日志')
            ->setUri($this->linkGenerator->getCurdListPage(EventLog::class))
            ->setAttribute('icon', 'fas fa-history')
        ;
    }
}
