# Campaign Bundle 解耦重构计划

## 📋 重构目标

将 `campaign-bundle` 从单一巨型 Bundle 拆分为：
- **1 个核心 Bundle**：campaign-bundle（核心活动管理）
- **3 个扩展 Bundle**：按奖励类型拆分的独立 Bundle

遵循**插件化架构**，实现模块间松耦合、按需组合。

---

## 🎯 拆分方案

### 方案架构图

```
┌─────────────────────────────────────────────────────────────┐
│                     campaign-bundle (核心)                    │
│  - Entity: Campaign, Award, Chance, Reward, Limit           │
│  - Service: CampaignService, RewardProcessorRegistry        │
│  - Contract: RewardProcessorInterface                       │
│  - Repository, Command, Controller, Procedure               │
└─────────────────────────────────────────────────────────────┘
                              ▲
                              │ 依赖（实现接口）
                    ┌─────────┴─────────┐
                    │                   │
        ┌───────────▼──────────┐  ┌────▼──────────────┐  ┌─────▼──────────────┐
        │ campaign-coupon-     │  │ campaign-credit-  │  │ campaign-product-  │
        │ bundle               │  │ bundle            │  │ bundle             │
        │                      │  │                   │  │                    │
        │ - CouponReward-      │  │ - CreditReward-   │  │ - SkuReward-       │
        │   Processor          │  │   Processor       │  │   Processor        │
        │                      │  │                   │  │ - SpuReward-       │
        │ depends on:          │  │ depends on:       │  │   Processor        │
        │ coupon-core-bundle   │  │ credit-bundle     │  │                    │
        └──────────────────────┘  └───────────────────┘  │ depends on:        │
                                                          │ product-core-      │
                                                          │ bundle             │
                                                          │ special-order-     │
                                                          │ bundle             │
                                                          └────────────────────┘
```

---

## 📦 Bundle 拆分清单

### 1. **tourze/campaign-bundle** (核心包，保留)

**职责**：活动核心管理、奖励抽象层、基础设施

**保留内容**：
```
src/
├── Contract/
│   └── RewardProcessorInterface.php          [新增] 奖励处理器接口
├── Entity/
│   ├── Campaign.php                          [保留] 活动主体
│   ├── Award.php                             [保留] 奖励配置
│   ├── Chance.php                            [保留] 参与机会
│   ├── Reward.php                            [保留] 奖励记录
│   ├── Limit.php                             [保留] 限制条件
│   ├── Category.php                          [保留] 活动分类
│   ├── Attribute.php                         [保留] 活动属性
│   └── EventLog.php                          [保留] 事件日志
├── Enum/
│   ├── AwardType.php                         [保留] 奖励类型枚举
│   ├── AwardLimitType.php                    [保留] 限制类型枚举
│   ├── CampaignStatus.php                    [保留] 活动状态枚举
│   └── LimitType.php                         [保留] 限制类型枚举
├── Service/
│   ├── CampaignService.php                   [重构] 核心编排服务
│   ├── CampaignLimitService.php              [保留] 限制管理服务
│   ├── CampaignRewardService.php             [保留] 奖励核心服务
│   ├── CampaignRewardProcessorService.php    [重构] 使用注册表模式
│   ├── RewardProcessorRegistry.php           [新增] 处理器注册表
│   └── AdminMenu.php                         [保留] 管理菜单
├── Repository/                               [保留] 所有仓储
├── Command/                                  [保留] 所有命令
├── Controller/                               [保留] 所有控制器
├── Procedure/                                [保留] 所有 JSON-RPC 过程
├── ExpressionLanguage/                       [保留] 表达式语言扩展
├── Traits/                                   [保留] 所有 Trait
├── Exception/                                [保留] 所有异常
│   └── UnsupportedRewardTypeException.php    [新增] 不支持的奖励类型异常
├── Event/                                    [保留] 所有事件
└── DataFixtures/                             [保留] 所有 Fixture
```

**核心依赖**（移除所有业务 Bundle）：
```json
{
  "require": {
    "symfony/framework-bundle": "^7.3",
    "doctrine/orm": "^3.0",
    "symfony/security-bundle": "^7.3",
    "easycorp/easyadmin-bundle": "^4",
    "tourze/json-rpc-*": "...",
    "tourze/doctrine-*": "..."
  },
  "suggest": {
    "tourze/campaign-coupon-bundle": "优惠券奖励支持",
    "tourze/campaign-credit-bundle": "积分奖励支持",
    "tourze/campaign-product-bundle": "商品资格奖励支持"
  }
}
```

---

### 2. **tourze/campaign-coupon-bundle** (新建)

**职责**：处理优惠券类型的活动奖励

**包位置**：`packages/campaign-coupon-bundle/`

**目录结构**：
```
campaign-coupon-bundle/
├── src/
│   ├── CampaignCouponBundle.php              Bundle 主类
│   ├── Service/
│   │   └── CouponRewardProcessor.php         优惠券奖励处理器
│   ├── Resources/
│   │   └── config/
│   │       └── services.yaml                 服务定义
│   └── DependencyInjection/
│       └── CampaignCouponExtension.php       扩展配置
├── tests/
│   └── Service/
│       └── CouponRewardProcessorTest.php
├── composer.json
└── README.md
```

**核心文件**：
- `CouponRewardProcessor.php`：实现 `RewardProcessorInterface`
- 依赖：`tourze/campaign-bundle` + `tourze/coupon-core-bundle`

**命名空间**：`CampaignCouponBundle\`

---

### 3. **tourze/campaign-credit-bundle** (新建)

**职责**：处理积分类型的活动奖励

**包位置**：`packages/campaign-credit-bundle/`

**目录结构**：
```
campaign-credit-bundle/
├── src/
│   ├── CampaignCreditBundle.php              Bundle 主类
│   ├── Service/
│   │   └── CreditRewardProcessor.php         积分奖励处理器
│   ├── Resources/
│   │   └── config/
│   │       └── services.yaml                 服务定义
│   └── DependencyInjection/
│       └── CampaignCreditExtension.php       扩展配置
├── tests/
│   └── Service/
│       └── CreditRewardProcessorTest.php
├── composer.json
└── README.md
```

**核心文件**：
- `CreditRewardProcessor.php`：实现 `RewardProcessorInterface`
- 依赖：`tourze/campaign-bundle` + `tourze/credit-bundle`

**命名空间**：`CampaignCreditBundle\`

---

### 4. **tourze/campaign-product-bundle** (新建)

**职责**：处理商品资格类型的活动奖励（SKU/SPU）

**包位置**：`packages/campaign-product-bundle/`

**目录结构**：
```
campaign-product-bundle/
├── src/
│   ├── CampaignProductBundle.php             Bundle 主类
│   ├── Service/
│   │   ├── SkuRewardProcessor.php            SKU 资格处理器
│   │   └── SpuRewardProcessor.php            SPU 资格处理器
│   ├── Resources/
│   │   └── config/
│   │       └── services.yaml                 服务定义
│   └── DependencyInjection/
│       └── CampaignProductExtension.php      扩展配置
├── tests/
│   └── Service/
│       ├── SkuRewardProcessorTest.php
│       └── SpuRewardProcessorTest.php
├── composer.json
└── README.md
```

**核心文件**：
- `SkuRewardProcessor.php`：处理 SKU 资格
- `SpuRewardProcessor.php`：处理 SPU 资格
- 依赖：
  - `tourze/campaign-bundle`
  - `tourze/product-core-bundle`
  - `tourze/product-service-contracts`
  - `tourze/special-order-bundle`

**命名空间**：`CampaignProductBundle\`

---

## 🔧 核心接口设计

### RewardProcessorInterface

```php
<?php

declare(strict_types=1);

namespace CampaignBundle\Contract;

use CampaignBundle\Entity\Award;
use CampaignBundle\Entity\Reward;
use CampaignBundle\Enum\AwardType;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 奖励处理器接口
 *
 * 所有奖励类型的处理器必须实现此接口
 * 通过 Symfony 的 Tagged Services 自动注册
 */
interface RewardProcessorInterface
{
    /**
     * 检查是否支持指定的奖励类型
     *
     * @param AwardType $type 奖励类型
     * @return bool 是否支持
     */
    public function supports(AwardType $type): bool;

    /**
     * 处理奖励发放
     *
     * @param UserInterface $user   接收奖励的用户
     * @param Award         $award  奖励配置
     * @param Reward        $reward 奖励记录（需要更新 sn 等信息）
     *
     * @throws \Exception 处理失败时抛出异常
     */
    public function process(UserInterface $user, Award $award, Reward $reward): void;

    /**
     * 获取处理器优先级
     *
     * 当多个处理器都支持同一类型时，优先级高的优先使用
     *
     * @return int 优先级（数字越大优先级越高，默认 0）
     */
    public function getPriority(): int;
}
```

---

## 📐 实施步骤

### 第一阶段：准备核心接口（当前）

1. ✅ 创建 `REFACTORING.md` 规划文档
2. ⏳ 在 campaign-bundle 中创建 `Contract/RewardProcessorInterface.php`
3. ⏳ 创建 `Service/RewardProcessorRegistry.php`
4. ⏳ 创建 `Exception/UnsupportedRewardTypeException.php`

### 第二阶段：创建扩展 Bundle

5. ⏳ 创建 `campaign-coupon-bundle`
6. ⏳ 创建 `campaign-credit-bundle`
7. ⏳ 创建 `campaign-product-bundle`

### 第三阶段：重构核心 Bundle

8. ⏳ 重构 `CampaignRewardProcessorService.php`
9. ⏳ 更新 `CampaignBundle.php` 移除业务依赖
10. ⏳ 更新 `composer.json` 移除业务依赖
11. ⏳ 更新 `services.yaml` 配置自动标记

### 第四阶段：测试和文档

12. ⏳ 编写单元测试
13. ⏳ 编写集成测试
14. ⏳ 更新 README.md
15. ⏳ 创建迁移指南

---

## 🎨 命名规范

### Bundle 命名

- ✅ **正确**：`campaign-coupon-bundle`、`CampaignCouponBundle`
- ❌ **错误**：`campaign-reward-coupon`、`CampaignRewardCoupon`

### 命名空间

- ✅ **正确**：`CampaignCouponBundle\Service\CouponRewardProcessor`
- ❌ **错误**：`CampaignRewardCoupon\Service\CouponProcessor`

### 类命名

- 处理器类：`{Type}RewardProcessor`（如 `CouponRewardProcessor`）
- Bundle 类：`Campaign{Type}Bundle`（如 `CampaignCouponBundle`）
- 扩展类：`Campaign{Type}Extension`（如 `CampaignCouponExtension`）

---

## 🔄 向下兼容策略

为保证平滑迁移，采用以下策略：

### 1. 保留旧方法（标记废弃）

```php
// campaign-bundle/src/Service/CampaignRewardProcessorService.php

/**
 * @deprecated since 2.0, use RewardProcessorRegistry instead
 */
private function processCouponReward(...): void
{
    // 保留实现，但标记废弃
}
```

### 2. 提供过渡期

- **v2.0**：引入新架构，旧方法标记 `@deprecated`
- **v2.1-2.5**：过渡期，同时支持新旧两种方式
- **v3.0**：移除旧方法，强制使用新架构

### 3. 迁移脚本

提供自动迁移脚本：
```bash
bin/console campaign:migrate-to-v2
```

---

## 📊 预期收益

### 依赖优化

| 指标 | 当前 | 重构后 | 改善 |
|------|------|--------|------|
| 核心包依赖数量 | 25+ | 12 | ↓ 52% |
| 最小安装体积 | ~50MB | ~15MB | ↓ 70% |
| 必需业务依赖 | 5 | 0 | ↓ 100% |

### 灵活性提升

- ✅ 支持仅安装优惠券活动（campaign-bundle + campaign-coupon-bundle）
- ✅ 支持仅安装积分活动（campaign-bundle + campaign-credit-bundle）
- ✅ 支持自定义奖励类型（实现 RewardProcessorInterface 即可）

### 测试改善

- ✅ 处理器独立测试，无需 Mock 大量依赖
- ✅ 核心逻辑和业务逻辑隔离测试
- ✅ 集成测试可按场景组合

---

## 🚀 下一步行动

立即开始实施第一阶段：

1. 创建核心接口 `RewardProcessorInterface`
2. 创建注册表 `RewardProcessorRegistry`
3. 创建异常类 `UnsupportedRewardTypeException`

完成第一阶段后，依次创建 3 个扩展 Bundle。

---

**文档版本**：v1.0
**创建时间**：2025-11-18
**最后更新**：2025-11-18
**负责人**：Claude Code
**状态**：✅ 已批准，准备实施
