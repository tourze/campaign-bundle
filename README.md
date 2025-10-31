# Campaign Bundle

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

A comprehensive campaign management bundle for Symfony applications, supporting various marketing
campaign types including lottery events, coupon distribution, and credit rewards with flexible
participation rules and automated lifecycle management.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Commands](#commands)
- [Usage](#usage)
- [Core Components](#core-components)
- [Dependencies](#dependencies)
- [Advanced Usage](#advanced-usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Comprehensive Campaign Management**: Support for various marketing campaigns with
  configurable participation rules
- **Flexible Reward System**: Configurable rewards including coupons, credits,
  SKU/SPU purchase qualifications
- **Smart Limitation System**: Daily, weekly, monthly, quarterly, yearly, and total
  limits for reward distribution
- **User Participation Tracking**: Complete event logging and user interaction tracking with context data
- **Automated Lifecycle Management**: Built-in commands for campaign and user chance expiration
- **EasyAdmin Integration**: Ready-to-use admin interface for campaign management
- **JSON-RPC API**: 7 comprehensive API endpoints for campaign operations
- **Expression Language Support**: Configurable participation conditions using Symfony Expression Language
- **Extensible Architecture**: Easy to extend with custom reward types and limitation rules

## Installation

```bash
composer require tourze/campaign-bundle
```

## Configuration

Add the bundle to your `bundles.php`:

```php
return [
    // ... other bundles
    CampaignBundle\CampaignBundle::class => ['all' => true],
];
```

## Commands

The bundle provides several console commands for campaign management:

### `campaign:chance-expire`
Automatically processes expired user chances/opportunities in campaigns.

```bash
php bin/console campaign:chance-expire
```

This command runs every minute via cron job to:
- Find expired user chances
- Mark them as invalid
- Update expiration remarks

### `campaign:check-expired-campaign`
Automatically disables campaigns that have passed their end time.

```bash
php bin/console campaign:check-expired-campaign
```

This command runs every minute via cron job to:
- Find campaigns past their end time
- Mark them as invalid
- Ensure proper campaign lifecycle management

## Usage

### Basic Campaign Creation

```php
use CampaignBundle\Entity\Campaign;
use CampaignBundle\Entity\Award;
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Enum\AwardLimitType;

// Create campaign
$campaign = new Campaign();
$campaign->setCode('SUMMER2024');
$campaign->setName('Summer Sale');
$campaign->setStartTime(new \DateTime('2024-07-01'));
$campaign->setEndTime(new \DateTime('2024-07-31'));
$campaign->setValid(true);

// Add coupon reward
$award = new Award();
$award->setCampaign($campaign);
$award->setEvent('join');
$award->setType(AwardType::COUPON);
$award->setValue('COUPON_CODE_001');
$award->setPrizeQuantity(1000);
$award->setAwardLimitType(AwardLimitType::BUY_TOTAL);
$award->setTimes(1);
```

### Setting Participation Conditions

```php
// Configure participation expression
$campaign->setRequestExpression('
    user.getCreatedAt() < date("-30 days") 
    and user.hasTag("VIP")
');
```

### JSON-RPC API

The bundle provides 7 JSON-RPC procedures for API integration:

- `RequestCampaignChance`: Request participation opportunity in a campaign
- `ConsumeCampaignChance`: Consume a campaign opportunity and receive rewards
- `GetCampaignConfig`: Retrieve complete campaign configuration
- `GetCampaignCategoryList`: Get campaign categories with pagination
- `GetCampaignRewards`: Retrieve user's rewards from specific campaign
- `GetCampaignEventLogs`: Retrieve campaign event logs with filtering
- `ReportCampaignEventLog`: Report user events in campaign

#### Example API Usage

```php
// Request participation chance
$response = $jsonRpcClient->call('RequestCampaignChance', [
    'campaignCode' => 'SUMMER2024'
]);

// Consume chance and get reward
$response = $jsonRpcClient->call('ConsumeCampaignChance', [
    'chanceId' => $chanceId,
    'event' => 'join'
]);
```

## Core Components

### Entities

- **Campaign**: Main campaign entity with status management and time-based lifecycle
- **Award**: Reward configurations with quantity and limitation controls
- **Chance**: User participation opportunities with expiration management
- **Reward**: User reward records with unique serial numbers
- **EventLog**: Comprehensive event tracking with arbitrary data support
- **Limit**: Flexible limitation rules for reward distribution
- **Category**: Hierarchical campaign categorization
- **Attribute**: Custom key-value properties for campaigns

### Reward Types

- **Coupons**: Both local and external coupon support
- **Credits**: Configurable credit point rewards
- **Purchase Qualifications**: SKU/SPU purchase rights
- **Custom Rewards**: Extensible reward system

### Limitation Types

- **Daily/Weekly/Monthly/Quarterly/Yearly**: Time-based limitations
- **Total Limit**: Overall quantity restrictions
- **User Tag Based**: User group specific limitations
- **Chance Based**: Participation opportunity limitations

## Dependencies

This bundle requires:

- **PHP**: 8.1 or higher
- **Symfony**: 6.4 or higher
- **Doctrine ORM**: 3.0 or higher
- **EasyAdmin**: 4.0 or higher

### Required Symfony Bundles

- `doctrine/doctrine-bundle`
- `easycorp/easyadmin-bundle`
- `symfony/security-bundle`
- `symfony/framework-bundle`

### Optional Dependencies

- `tourze/coupon-core-bundle`: For coupon reward support
- `tourze/credit-bundle`: For credit point rewards
- `tourze/product-core-bundle`: For SKU/SPU purchase qualifications
- `tourze/order-core-bundle`: For order-related campaign features

## Advanced Usage

### Custom Reward Types

Extend the reward system with custom reward types:

```php
use CampaignBundle\Enum\AwardType;
use CampaignBundle\Service\CampaignService;

// Create custom reward handler
class CustomRewardHandler
{
    public function handleCustomReward(Award $award, UserInterface $user): Reward
    {
        // Custom reward logic here
        $reward = new Reward();
        $reward->setType(AwardType::CUSTOM);
        $reward->setValue($award->getValue());
        $reward->setUser($user);
        
        return $reward;
    }
}
```

### Expression Language Functions

Use built-in expression language functions for complex participation conditions:

```php
// Available functions:
// - hasChance(user, campaign): Check if user has valid chance
// - getChanceCount(user, campaign): Get user's remaining chances
// - hasTag(user, tagName): Check if user has specific tag
// - getTagValue(user, tagName): Get user tag value

$campaign->setRequestExpression('
    hasChance(user, campaign) 
    and user.getCreatedAt() < date("-30 days")
    and hasTag(user, "VIP")
    and getTagValue(user, "level") >= 5
');
```

### Event System Integration

Integrate with Symfony's event system for advanced workflows:

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
        // Custom event processing logic
        $this->logger->info('User event reported', [
            'user_id' => $event->getUser()->getId(),
            'event' => $event->getEvent(),
            'data' => $event->getData(),
        ]);
    }
}
```

### Database Optimization

For high-traffic campaigns, consider these optimizations:

```yaml
# Use dedicated database connections for campaign operations
# Configure in doctrine.yaml:
doctrine:
    dbal:
        connections:
            campaign:
                url: '%env(resolve:DATABASE_CAMPAIGN_URL)%'
            default:
                url: '%env(resolve:DATABASE_URL)%'
```

```php

// Use database-level locks for concurrent reward distribution
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

class CampaignService
{
    #[Transactional]
    public function distributeRewardWithLock(Award $award, UserInterface $user): Reward
    {
        // Atomic reward distribution with database locks
        return $this->rewardUser($user, $award);
    }
}
```

## Performance Monitoring

Monitor campaign performance with built-in metrics:

```php
// Track campaign metrics
$metrics = [
    'total_participants' => $this->getParticipantCount($campaign),
    'total_rewards_distributed' => $this->getRewardCount($campaign),
    'conversion_rate' => $this->getConversionRate($campaign),
    'average_participation_time' => $this->getAverageParticipationTime($campaign),
];
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
