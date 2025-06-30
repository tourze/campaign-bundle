<?php

namespace CampaignBundle\Procedure;

use CampaignBundle\Entity\Category;
use CampaignBundle\Repository\CategoryRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
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
        private readonly NormalizerInterface $normalizer,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->categoryRepository
            ->createQueryBuilder('a')
            ->where('a.valid = true')
            ->addOrderBy('a.sortNumber', 'DESC')
            ->addOrderBy('a.id', 'DESC');

        return $this->fetchList($qb, $this->formatItem(...));
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser() !== null) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 30;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Category::class);
    }

    private function formatItem(Category $category): array
    {
        return $this->normalizer->normalize($category, 'array', ['groups' => 'restful_read']);
    }
}
