<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class BlogListResolver
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $blogAuthorRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCategoryRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogPostRepository;

    /**
     * @param EntityRepository $blogPostRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepository $blogPostRepository,
        SystemConfigService       $systemConfigService
    ) {
        $this->blogPostRepository = $blogPostRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @throws Exception
     */
    public function getPosts($request, $context)
    {
        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPage');
        $postListLimit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $sortBy = $this->systemConfigService->get('MagefanBlog.config.postsSortBy');
        $displayMode = $this->systemConfigService->get('MagefanBlog.config.displayMode');

        if (!$limit) {
            $limit = $postListLimit;
        }

        $sorting = FieldSorting::ASCENDING;
        if ($sortBy === 'position') {
            $sorting = FieldSorting::DESCENDING;
        }
        $pageNumber = $this->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;

        $postsCriteria = (new Criteria())
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addSorting(new FieldSorting($sortBy, $sorting))
            ->setLimit($limit)
            ->setOffset($pageOffset)
            ->addAssociation('media')
            ->addAssociation('postTags')
            ->addAssociation('postAuthor')
            ->addAssociation('postCategories');

        if ((int)$displayMode == 0) {
            $postsCriteria->addFilter(new EqualsFilter('includeInRecent', true));
        }

        $posts = $this->blogPostRepository->search($postsCriteria, $context->getContext())->getEntities()->getElements();

        if ($displayMode == 1) {
            $posts = [];
        }

        return $posts;
    }

    /**
     * @param Request $request
     * @return int
     */
    private function getPage(Request $request): int
    {
        $page = $request->query->getInt('page', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('page', $page);
        }

        return $page <= 0 ? 1 : $page;
    }

    /**
     * @param $context
     * @return array
     */
    public function getPagination($context): array
    {
        $displayMode = $this->systemConfigService->get('MagefanBlog.config.displayMode');
        $criteria = (new Criteria())->addFilter(new EqualsFilter('isActive', 1));

        if ((int)$displayMode == 0) {
            $criteria->addFilter(new EqualsFilter('includeInRecent', true));
        }

        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPage');
        $postListLimit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $totalPosts = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities()->count();

        if (!$limit) {
            $limit = $postListLimit;
        }

        $pages = [];
        $pagesCount = $totalPosts / $limit;
        for ($i = 1; $i <= ceil($pagesCount); $i++) {
            $pages[$i] = $totalPosts / $limit > 1 ? $limit : $totalPosts;
            $totalPosts = $totalPosts - $limit;
        }
        return $pages;
    }
}
