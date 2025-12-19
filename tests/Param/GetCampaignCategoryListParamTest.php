<?php

declare(strict_types=1);

namespace CampaignBundle\Tests\Param;

use CampaignBundle\Param\GetCampaignCategoryListParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPCPaginatorBundle\Param\PaginatorParamInterface;

/**
 * @internal
 */
#[CoversClass(GetCampaignCategoryListParam::class)]
final class GetCampaignCategoryListParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetCampaignCategoryListParam(
            pageSize: 20,
            currentPage: 2,
            lastId: 123
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
        $this->assertSame(20, $param->pageSize);
        $this->assertSame(2, $param->currentPage);
        $this->assertSame(123, $param->lastId);
    }

    public function testParamCanBeConstructedWithDefaults(): void
    {
        $param = new GetCampaignCategoryListParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertInstanceOf(PaginatorParamInterface::class, $param);
        $this->assertSame(10, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new GetCampaignCategoryListParam(
            pageSize: 50,
            currentPage: 3,
            lastId: 456
        );

        $this->assertSame(50, $param->pageSize);
        $this->assertSame(3, $param->currentPage);
        $this->assertSame(456, $param->lastId);
    }

    public function testParamWithNullLastId(): void
    {
        $param = new GetCampaignCategoryListParam(
            pageSize: 15,
            currentPage: 1,
            lastId: null
        );

        $this->assertSame(15, $param->pageSize);
        $this->assertSame(1, $param->currentPage);
        $this->assertNull($param->lastId);
    }
}