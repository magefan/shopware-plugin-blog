<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogArchiveResolver extends BlogAbstractResolver
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @param SystemConfigService $systemConfigService
     * @param EntityRepositoryInterface $blogPostRepository
     */
    public function __construct(
        SystemConfigService       $systemConfigService,
        EntityRepositoryInterface $blogPostRepository
    ) {
        $this->blogPostRepository = $blogPostRepository;
        parent::__construct($systemConfigService);
    }

    /**
     * @param $identifier
     * @param $request
     * @param $context
     * @return false|mixed
     */
    public function getArchive($identifier, $request, $context)
    {
        $date = explode('-', $identifier);
        $date[2] = '01 00:00:00';
        $date[3] = '01 23:59:59';
        $time = strtotime(implode('-', $date));

        if ((!$time || count($date) != 3) && !$identifier) {
            return false;
        }
        $yearMonth = $date[0] . '-' . $date[1];

        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $yearMonth . '-' . $date[2])->format("Y-m-d H:i:s");
        $end = \DateTime::createFromFormat('Y-m-d H:i:s', $yearMonth . '-' . $date[3])->format("Y-m-t H:i:s");

        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $pageNumber = $this->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;
        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addFilter(new RangeFilter(
                'createdAt',
                [
                    RangeFilter::GTE => $start,
                    RangeFilter::LTE => $end,
                ]
            ))
            ->setLimit($limit)
            ->setOffset($pageOffset);

        $archivePosts = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities();

        if (!$archivePosts) {
            return false;
        }

        return  $archivePosts;
    }

    /**
     * @param $context
     * @return EntityCollection
     */
    public function getAllPosts($context): EntityCollection
    {
        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('isActive', 1));
        return$this->blogPostRepository->search($criteria, $context->getContext())->getEntities();
    }
}
