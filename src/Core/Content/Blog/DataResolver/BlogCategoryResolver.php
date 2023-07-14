<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Magefan\Blog\Model\Config\Source\PostsSortBy;
use Magento\Framework\Api\SortOrder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogCategoryResolver extends BlogAbstractResolver
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCategoryRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogPostRepository;

    /**
     * @param SystemConfigService $systemConfigService
     * @param Connection $connection
     * @param EntityRepository $blogCategoryRepository
     * @param EntityRepository $blogPostRepository
     */
    public function __construct(
        SystemConfigService       $systemConfigService,
        Connection                $connection,
        EntityRepository $blogCategoryRepository,
        EntityRepository $blogPostRepository
    )
    {
        $this->connection = $connection;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->blogPostRepository = $blogPostRepository;
        parent::__construct($systemConfigService);
    }

    /**
     * @param $identifier
     * @param $context
     * @return mixed|null
     */
    public function getCategory($identifier, $context)
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('id', $identifier))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addAssociation('blogCategories')
            ->addAssociation('blogPosts');

        return $this->blogCategoryRepository->search($criteria, $context->getContext())->getEntities()->first();
    }

    /**
     * @param $category
     * @param $request
     * @param $context
     * @return EntityCollection
     */
    public function getPostsByCategory($category, $request, $context): EntityCollection
    {
        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $pageNumber = $this->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;

        $sortBy = $category->getPostsSortBy();
        $sorting = FieldSorting::DESCENDING;
        if ($sortBy === 'title') {
            $sorting = FieldSorting::ASCENDING;
        }

        $postsCriteria = (new Criteria())
            ->addAssociation('media')
            ->addAssociation('postCategories')
            ->addAssociation('blogPosts')
            ->addAssociation('postCategories')
            ->addAssociation('blogCategories')
            ->addAssociation('postTags')
            ->addAssociation('postAuthor')
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addSorting(new FieldSorting('postTags'. '.' . $sortBy, $sorting))
            ->addFilter(new EqualsFilter('postCategories.id', $category->getId()))
            ->setLimit((bool)$category->getPostsPerPage() ? $category->getPostsPerPage() : $limit)
            ->setOffset($pageOffset);

        $categoryPosts = $this->blogPostRepository->search($postsCriteria, $context->getContext())->getEntities();

        if (!$categoryPosts) {
            return false;
        }

        return $categoryPosts;
    }

    /**
     * @return EntityCollection
     */
    public function getCategories(): EntityCollection
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addFilter(new EqualsFilter('includeInMenu', 1))
            ->addSorting(new FieldSorting('position', FieldSorting::ASCENDING));

        return $this->blogCategoryRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }
}
