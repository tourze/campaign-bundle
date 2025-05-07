<?php

namespace CampaignBundle;

use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\EventLog;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

#[MenuProvider]
class AdminMenu
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('通用活动')) {
            $item->addChild('通用活动');
        }

        $item->getChild('通用活动')->addChild('活动管理')->setUri($this->linkGenerator->getCurdListPage(Campaign::class));
        $item->getChild('通用活动')->addChild('参与日志')->setUri($this->linkGenerator->getCurdListPage(EventLog::class));
    }
}
