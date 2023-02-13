<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogTagResolver extends BlogAbstractResolver
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogTagRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @param SystemConfigService $systemConfigService
     * @param EntityRepositoryInterface $blogTagRepository
     * @param EntityRepositoryInterface $blogPostRepository
     */
    public function __construct(
        SystemConfigService       $systemConfigService,
        EntityRepositoryInterface $blogTagRepository,
        EntityRepositoryInterface $blogPostRepository
    ) {
        $this->blogTagRepository = $blogTagRepository;
        $this->blogPostRepository = $blogPostRepository;
        parent::__construct($systemConfigService);
    }

    /**
     * @param $tagId
     * @param $context
     * @return false
     */
    public function getTag($tagId, $context)
    {
        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addFilter(new EqualsFilter('identifier', $tagId))
            ->addAssociation('postTags');

        $author = $this->blogTagRepository->search($criteria, $context->getContext())->getEntities()->first();

        if (!$author) {
            return false;
        }

        return $author;
    }

    /**
     * @throws Exception
     */
    public function getPostsByTag($tag, $request, $context): EntityCollection
    {
        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $sortBy = $this->systemConfigService->get('MagefanBlog.config.postsSortBy');

        $sorting = FieldSorting::ASCENDING;
        if ($sortBy === 'position') {
            $sorting = FieldSorting::DESCENDING;
        }

        $pageNumber = $this->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;
        $postsCriteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addAssociation('postTags')
            ->addSorting(new FieldSorting($sortBy, $sorting))
            ->addFilter(new EqualsFilter('postTags.id', $tag->getId()))
            ->setLimit($tag->getPostsPerPage() ?: $limit)
            ->setOffset($pageOffset);

        $tagPosts = $this->blogPostRepository->search($postsCriteria, $context->getContext())->getEntities();

        if (!$tagPosts) {
            return false;
        }

        return $tagPosts;
    }
}
