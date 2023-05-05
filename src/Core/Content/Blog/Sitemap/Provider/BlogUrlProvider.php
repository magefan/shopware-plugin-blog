<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Sitemap\Provider;

use Doctrine\DBAL\Connection;
use Magefan\Blog\Core\Content\Blog\Category\CategoryEntity;
use Magefan\Blog\Core\Content\Blog\Post\PostEntity;
use Shopware\Core\Content\Sitemap\Provider\AbstractUrlProvider;
use Shopware\Core\Content\Sitemap\Struct\Url;
use Shopware\Core\Content\Sitemap\Struct\UrlResult;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\SuffixFilter;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogUrlProvider extends AbstractUrlProvider
{

    public const CHANGE_FREQ = 'daily';

    public const PRIORITY = 1.0;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCategoryRepository;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param SystemConfigService $systemConfigService
     * @param RouterInterface $router
     */
    public function __construct(
        EntityRepositoryInterface $blogPostRepository,
        EntityRepositoryInterface $blogCategoryRepository,
        SystemConfigService       $systemConfigService,
        RouterInterface           $router
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->systemConfigService = $systemConfigService;
        $this->router = $router;
    }

    /**
     * @return AbstractUrlProvider
     */
    public function getDecorated(): AbstractUrlProvider
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'MagefanBlog';
    }

    /**
     * @param SalesChannelContext $context
     * @param int $limit
     * @param int|null $offset
     * @return UrlResult
     */
    public function getUrls(SalesChannelContext $context, int $limit, ?int $offset = null): UrlResult
    {
        $urls = [];

        $indexPageEnabled = (bool)$this->systemConfigService->get('MagefanBlog.config.blogIndexSitemapEnable');
        $categoriesSitemapEnable = (bool)$this->systemConfigService->get('MagefanBlog.config.blogCategoriesSitemapEnable');
        $postsSitemapEnable = (bool)$this->systemConfigService->get('MagefanBlog.config.blogPostsSitemapEnable');

        if ($indexPageEnabled) {
            $urls = array_merge($urls, $this->getBlogIndexPage());
        }

        if ($categoriesSitemapEnable) {
            $urls = array_merge($urls, $this->getBlogCategories($limit, $offset, $context));
        }

        if ($postsSitemapEnable) {
            $urls = array_merge($urls, $this->getBlogPosts($limit, $offset, $context));
        }

        return new UrlResult($urls, null);
    }

    /**
     * @return Url[]
     */
    protected function getBlogIndexPage()
    {
        $sitemapFrequency = $this->systemConfigService->get('MagefanBlog.config.blogIndexSitemapFrequency');
        $sitemapPriority = $this->systemConfigService->get('MagefanBlog.config.blogIndexSitemapPriority');

        $blogIndexUrl = new Url();
        $blogIndexUrl->setLastmod(new \DateTime());
        $blogIndexUrl->setChangefreq($sitemapFrequency);
        $blogIndexUrl->setPriority($sitemapPriority ?: 0.8);
        $blogIndexUrl->setResource(CategoryEntity::class);
        $blogIndexUrl->setLoc($this->router->generate('frontend.blog'));

        return [$blogIndexUrl];
    }

    /**
     * @param $limit
     * @param $offset
     * @param $context
     * @return array
     */
    protected function getBlogCategories($limit, $offset, $context)
    {
        $sitemapFrequency = $this->systemConfigService->get('MagefanBlog.config.blogCategoriesSitemapFrequency');
        $sitemapPriority = $this->systemConfigService->get('MagefanBlog.config.blogCategoriesSitemapPriority');

        $criteria = new Criteria();
        $criteria->setLimit($limit);
        $criteria->setOffset($offset);
        $criteria->addFilter(new EqualsFilter('isActive', true));

        $blogCategoryEntities = $this->blogCategoryRepository->search($criteria, $context->getContext())->getEntities();

        if ($blogCategoryEntities->count() === 0) {
            return [];
        }

        $categoriesUrl = [];

        foreach ($blogCategoryEntities as $blogCategoryEntity) {
            $blogCategoryUrl = new Url();
            $blogCategoryUrl->setLastmod($blogCategoryEntity->getCreatedAt() ?? new \DateTime());
            $blogCategoryUrl->setChangefreq($sitemapFrequency);
            $blogCategoryUrl->setPriority($sitemapPriority ?: 0.8);
            $blogCategoryUrl->setResource(CategoryEntity::class);
            $blogCategoryUrl->setIdentifier($blogCategoryEntity->getId());
            $blogCategoryUrl->setLoc(
                $this->router->generate('frontend.blog.category',
                    ['id' => $blogCategoryEntity->getId()],
                    UrlGeneratorInterface::ABSOLUTE_PATH)
            );

            $categoriesUrl[] = $blogCategoryUrl;
        }

        return $categoriesUrl;
    }

    /**
     * @param $limit
     * @param $offset
     * @param $context
     * @return array
     */
    protected function getBlogPosts($limit, $offset, $context)
    {
        $sitemapFrequency = $this->systemConfigService->get('MagefanBlog.config.blogCategoriesSitemapFrequency');
        $sitemapPriority = $this->systemConfigService->get('MagefanBlog.config.blogCategoriesSitemapPriority');

        $criteria = new Criteria();
        $criteria->setLimit($limit);
        $criteria->setOffset($offset);
        $criteria->addFilter(new EqualsFilter('isActive', true));


        $blogPostEntities = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities();

        if ($blogPostEntities->count() === 0) {
            return [];
        }

        $postsUrl = [];

        foreach ($blogPostEntities as $blogPostEntity) {
            $blogPostUrl = new Url();
            $blogPostUrl->setLastmod($blogPostEntity->getCreatedAt() ?? new \DateTime());
            $blogPostUrl->setChangefreq($sitemapFrequency);
            $blogPostUrl->setPriority($sitemapPriority ?: 0.5);
            $blogPostUrl->setResource(PostEntity::class);
            $blogPostUrl->setIdentifier($blogPostEntity->getId());
            $blogPostUrl->setLoc(
                $this->router->generate('frontend.blog.post',
                    ['identifier' => $blogPostEntity->getIdentifier()],
                    UrlGeneratorInterface::ABSOLUTE_PATH)
            );

            $postsUrl[] = $blogPostUrl;
        }

        return $postsUrl;
    }
}
