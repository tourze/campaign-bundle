# 活动模块

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/campaign-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/campaign-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/campaign-bundle?style=flat-square)]
(https://packagist.org/packages/tourze/campaign-bundle)
[![License](https://img.shields.io/packagist/l/tourze/campaign-bundle?style=flat-square)]
(https://packagist.org/packages/tourze/campaign-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master&style=flat-square)]
(https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/coveralls/github/tourze/php-monorepo/master?style=flat-square)]
(https://coveralls.io/github/tourze/php-monorepo?branch=master)

一个全面的 Symfony 活动管理包，支持多种营销活动类型，包括抽奖活动、优惠券发放、积分奖励等，
具有灵活的参与规则和自动化生命周期管理功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [命令](#命令)
- [使用方法](#使用方法)
- [核心组件](#核心组件)
- [依赖项](#依赖项)
- [高级用法](#高级用法)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **全面的活动管理**: 支持各种营销活动，具有可配置的参与规则
- **灵活的奖励系统**: 可配置的奖励，包括优惠券、积分、SKU/SPU购买资格
- **智能限制系统**: 支持每日、每周、每月、每季、每年和总计限制
- **用户参与跟踪**: 完整的事件日志和用户交互跟踪，包含上下文数据
- **自动化生命周期管理**: 内置命令处理活动和用户机会的过期
- **EasyAdmin 集成**: 开箱即用的活动管理后台界面
- **JSON-RPC API**: 7 个全面的 API 端点用于活动操作
- **表达式语言支持**: 使用 Symfony 表达式语言的可配置参与条件
- **可扩展架构**: 易于扩展自定义奖励类型和限制规则

## 安装

```bash
composer require tourze/campaign-bundle
```

## 配置

将此包添加到您的 `bundles.php` 中：

```php
return [
    // ... 其他包
    CampaignBundle\CampaignBundle::class => ['all' => true],
];
```

## 命令

该包提供了几个用于活动管理的控制台命令：

### `campaign:chance-expire`
自动处理活动中过期的用户机会/机遇。

```bash
php bin/console campaign:chance-expire
```

此命令通过 cron 作业每分钟运行一次，用于：
- 查找过期的用户机会
- 将其标记为无效
- 更新过期备注

### `campaign:check-expired-campaign`
自动禁用已过结束时间的活动。

```bash
php bin/console campaign:check-expired-campaign
```

此命令通过 cron 作业每分钟运行一次，用于：
- 查找已过结束时间的活动
- 将其标记为无效
- 确保适当的活动生命周期管理

## 使用方法

### 基本活动创建

```php
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Award;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Enum\AwardLimitType;

// 创建活动
$campaign = new Campaign();
$campaign->setCode('SUMMER2024');
$campaign->setName('夏季促销活动');
$campaign->setStartTime(new \DateTime('2024-07-01'));
$campaign->setEndTime(new \DateTime('2024-07-31'));
$campaign->setValid(true);

// 添加优惠券奖励
$award = new Award();
$award->setCampaign($campaign);
$award->setEvent('join');
$award->setType(AwardType::COUPON);
$award->setValue('COUPON_CODE_001');
$award->setPrizeQuantity(1000);
$award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
$award->setTimes(1);
```

### 设置参与条件

```php
// 配置参与表达式
$campaign->setRequestExpression('
    user.getCreatedAt() < date("-30 days") 
    and user.hasTag("VIP")
');
```

### JSON-RPC API

该包为 API 集成提供了 7 个 JSON-RPC 程序：

- `RequestCampaignChance`: 请求活动参与机会
- `ConsumeCampaignChance`: 消费活动机会并获取奖励
- `GetCampaignConfig`: 检索完整的活动配置
- `GetCampaignCategoryList`: 获取活动类别，支持分页
- `GetCampaignRewards`: 检索用户在特定活动中的奖励
- `GetCampaignEventLogs`: 检索活动事件日志，支持筛选
- `ReportCampaignEventLog`: 上报用户在活动中的事件

#### API 使用示例

```php
// 请求参与机会
$response = $jsonRpcClient->call('RequestCampaignChance', [
    'campaignCode' => 'SUMMER2024'
]);

// 消费机会并获取奖励
$response = $jsonRpcClient->call('ConsumeCampaignChance', [
    'chanceId' => $chanceId,
    'event' => 'join'
]);
```

## 核心组件

### 实体类

- **Campaign**: 活动主实体，具有状态管理和基于时间的生命周期
- **Award**: 奖励配置，包含数量和限制控制
- **Chance**: 用户参与机会，包含过期管理
- **Reward**: 用户奖励记录，包含唯一序列号
- **EventLog**: 全面的事件跟踪，支持任意数据
- **Limit**: 灵活的奖励发放限制规则
- **Category**: 分层的活动分类
- **Attribute**: 活动的自定义键值属性

### 奖励类型

- **优惠券**: 支持本地和外部优惠券
- **积分**: 可配置的积分奖励
- **购买资格**: SKU/SPU 购买权限
- **自定义奖励**: 可扩展的奖励系统

### 限制类型

- **时间限制**: 每日/每周/每月/每季/每年限制
- **总计限制**: 整体数量限制
- **用户标签限制**: 特定用户组限制
- **机会限制**: 参与机会限制

## 依赖项

此包需要：

- **PHP**: 8.1 或更高版本
- **Symfony**: 6.4 或更高版本
- **Doctrine ORM**: 3.0 或更高版本
- **EasyAdmin**: 4.0 或更高版本

### 必需的 Symfony 包

- `doctrine/doctrine-bundle`
- `easycorp/easyadmin-bundle`
- `symfony/security-bundle`
- `symfony/framework-bundle`

### 可选依赖项

- `tourze/coupon-core-bundle`: 用于优惠券奖励支持
- `tourze/credit-bundle`: 用于积分奖励
- `tourze/product-core-bundle`: 用于 SKU/SPU 购买资格
- `tourze/order-core-bundle`: 用于订单相关的活动功能

## 高级用法

### 自定义奖励类型

使用自定义奖励类型扩展奖励系统：

```php
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Service\CampaignService;

// 创建自定义奖励处理器
class CustomRewardHandler
{
    public function handleCustomReward(Award $award, UserInterface $user): Reward
    {
        // 自定义奖励逻辑
        $reward = new Reward();
        $reward->setType(AwardType::CUSTOM);
        $reward->setValue($award->getValue());
        $reward->setUser($user);
        
        return $reward;
    }
}
```

### 表达式语言函数

使用内置的表达式语言函数处理复杂的参与条件：

```php
// 可用函数：
// - hasChance(user, campaign): 检查用户是否有有效机会
// - getChanceCount(user, campaign): 获取用户剩余机会数量
// - hasTag(user, tagName): 检查用户是否有特定标签
// - getTagValue(user, tagName): 获取用户标签值

$campaign->setRequestExpression('
    hasChance(user, campaign) 
    and user.getCreatedAt() < date("-30 days")
    and hasTag(user, "VIP")
    and getTagValue(user, "level") >= 5
');
```

### 事件系统集成

与 Symfony 的事件系统集成以实现高级工作流：

```php
use CampaignBundle\Event\UserEventReportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserEventReportEvent::class => 'onUserEventReport',
        ];
    }
    
    public function onUserEventReport(UserEventReportEvent $event): void
    {
        // 自定义事件处理逻辑
        $this->logger->info('用户事件上报', [
            'user_id' => $event->getUser()->getId(),
            'event' => $event->getEvent(),
            'data' => $event->getData(),
        ]);
    }
}
```

### 数据库优化

对于高流量活动，考虑这些优化：

```yaml
# 为活动操作使用专用数据库连接
# 在 doctrine.yaml 中配置：
doctrine:
    dbal:
        connections:
            campaign:
                url: '%env(resolve:DATABASE_CAMPAIGN_URL)%'
            default:
                url: '%env(resolve:DATABASE_URL)%'
```

```php

// 使用数据库级锁进行并发奖励分发
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

class CampaignService
{
    #[Transactional]
    public function distributeRewardWithLock(Award $award, UserInterface $user): Reward
    {
        // 使用数据库锁的原子奖励分发
        return $this->rewardUser($user, $award);
    }
}
```

## 性能监控

使用内置指标监控活动性能：

```php
// 跟踪活动指标
$metrics = [
    'total_participants' => $this->getParticipantCount($campaign),
    'total_rewards_distributed' => $this->getRewardCount($campaign),
    'conversion_rate' => $this->getConversionRate($campaign),
    'average_participation_time' => $this->getAverageParticipationTime($campaign),
];
```

## 贡献

请参阅 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证。请参阅 [许可证文件](LICENSE) 了解更多信息。
