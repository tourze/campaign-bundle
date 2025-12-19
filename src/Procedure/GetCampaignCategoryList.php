<?php

declare(strict_types=1);

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\Category;
use CampaignBundle\Param\GetCampaignCategoryListParam;
use CampaignBundle\Repository\CategoryRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag(name: '活动模块')]
#[MethodDoc(summary: '获取活动目录列表')]
#[MethodExpose(method: 'GetCampaignCategoryList')]
class GetCampaignCategoryList extends CacheableProcedure
{
    use PaginatorTrait;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param GetCampaignCategoryListParam $param
     */
    public function execute(GetCampaignCategoryListParam|RpcParamInterface $param): ArrayResult
    {
        $qb = $this->categoryRepository
            ->createQueryBuilder('a')
            ->where('a.valid = true')
            ->addOrderBy('a.sortNumber', 'DESC')
            ->addOrderBy('a.id', 'DESC')
        ;

        return new ArrayResult($this->fetchList($qb, $this->formatItem(...), null, $param));
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            $params = new JsonRpcParams([]);
        }
        $key = static::buildParamCacheKey($params);
        if (null !== $this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return new ArrayResult($key);
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 30;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Category::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatItem(Category $category): array
    {
        return new ArrayResult([
            'id' => $category->getId(),
            'title' => $category->getTitle(),
        ]);
    }
}
