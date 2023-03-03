<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogSidebarResolver
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCategoryRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @var
     */
    private $months;

    /**
     * @var
     */
    private $maxCount;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogTagRepository;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param EntityRepositoryInterface $blogTagRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepositoryInterface $blogPostRepository,
        EntityRepositoryInterface $blogCategoryRepository,
        EntityRepositoryInterface $blogTagRepository,
        SystemConfigService       $systemConfigService
    ) {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->blogTagRepository = $blogTagRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @param $context
     * @return array
     */
    public function getSidebar($context): array
    {
        $searchSortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.searchFormSortOrder');
        $categorySortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.categorySortOrder');
        $recentPostSortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.recentPostSortOrder');
        $tagSortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.tagSortOrder');
        $archiveWidgetSortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.archiveWidgetSortOrder');
        $rssSidebarSortOrder = (int)$this->systemConfigService->get('MagefanBlog.config.rssSidebarSortOrder');

        $sidebar[$categorySortOrder] = $this->getCategories($context);
        $sidebar[$recentPostSortOrder] = $this->getRecentPosts($context);
        $sidebar[$archiveWidgetSortOrder] = $this->getArchive($context);
        $sidebar[$tagSortOrder]['tags'] = $this->getTags($context);
        $sidebar[$rssSidebarSortOrder]['rss'] = '';
        $sidebar[$searchSortOrder]['search'] = '';
        ksort($sidebar);

        return $sidebar;
    }

    /**
     * @param $context
     * @return array
     */
    private function getArchive($context)
    {
        $archive = [];
        $archiveWidgetEnabled = (bool)$this->systemConfigService->get('MagefanBlog.config.archiveWidgetEnabled');

        if (null === $this->months && $archiveWidgetEnabled) {
            $recentPostNumbers = (int)$this->systemConfigService->get('MagefanBlog.config.recentPostNumbers');

            $criteria = (new Criteria([]))->addFilter(new EqualsFilter('isActive', 1))
                ->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING))
                ->setLimit($recentPostNumbers);

            $posts = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities();
            foreach ($posts as $post) {
                $archive[$post->createdAt->format('Y-m')] = $post->createdAt->format('M Y');
            }
            $this->months['archive'] = $archive;
        }
        return $this->months;
    }

    /**
     * @param $context
     * @return array
     */
    public function getCategories($context): array
    {
        $categories = [];
        $sidebarCategoryEnabled = (bool)$this->systemConfigService->get('MagefanBlog.config.sidebarCategoryEnabled');

        if (!$sidebarCategoryEnabled) {
            return $categories;
        }

        $criteria = (new Criteria([]))->addFilter(new EqualsFilter('isActive', 1))
            ->addSorting(new FieldSorting('position', FieldSorting::DESCENDING))
            ->addAssociation('blogPosts');

        $categories['categories'] = $this->blogCategoryRepository->search($criteria, $context->getContext())->getEntities();

        return $categories;
    }

    /**
     * @param $context
     * @return array
     */
    public function getRecentPosts($context): array
    {
        $recentPosts = [];
        $recentPostFormEnabled = (bool)$this->systemConfigService->get('MagefanBlog.config.recentPostFormEnabled');

        if (!$recentPostFormEnabled) {
            return $recentPosts;
        }

        $recentPostNumbers = (int)$this->systemConfigService->get('MagefanBlog.config.recentPostNumbers');

        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addFilter(new EqualsFilter('includeInRecent', true))
            ->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING))
            ->setLimit($recentPostNumbers)
            ->addAssociation('media')
            ->addAssociation('postTags')
            ->addAssociation('postCategories');

        $recentPosts['recentPosts'] = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities();

        return $recentPosts;
    }

    /**
     * @param $context
     * @return array
     */
    public function getTags($context): array
    {
        $tags = [];
        $tagCloudEnabled = (bool)$this->systemConfigService->get('MagefanBlog.config.tagCloudEnabled');

        if (!$tagCloudEnabled) {
            return $tags;
        }

        $tagsNumber = (int)$this->systemConfigService->get('MagefanBlog.config.tagsNumber');

        $criteria = (new Criteria([]))->addFilter(new EqualsFilter('isActive', 1))
            ->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING))
            ->setLimit($tagsNumber)
            ->addAssociation('postTags');

        $recentTags = $this->blogTagRepository->search($criteria, $context->getContext())->getEntities();

        foreach ($recentTags as $tag) {
            $tags[$tag->title]['tag'] = $tag;
            $tags[$tag->title]['count'] = count($tag->postTags);
        }

        if (empty($tags)){
            return $tags;
        }
        
        $this->maxCount = max($tags);
        foreach ($tags as $tag) {
            $tags[$tag['tag']->title]['class'] = $this->getTagClass($tag['count']);
        }

        return $tags;
    }

    /**
     * Retrieve tag class
     * @param $tag
     * @return string
     */
    public function getTagClass($tag): string
    {
        $maxCount = ($this->maxCount['count'] > 0) ? $this->maxCount['count'] : 1;
        $percent = floor(($tag / $maxCount) * 100);

        if ($percent < 20) {
            return 'smallest';
        }
        if ($percent >= 20 && $percent < 40) {
            return 'small';
        }
        if ($percent >= 40 && $percent < 60) {
            return 'medium';
        }
        if ($percent >= 60 && $percent < 80) {
            return 'large';
        }
        return 'largest';
    }
}
