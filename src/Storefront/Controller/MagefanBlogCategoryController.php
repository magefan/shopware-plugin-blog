<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogAbstractResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogCategoryResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogCategoryController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $blogRepository;

    /**
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericPageLoader;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCategoryRepository;

    /**
     * @var BlogSidebarResolver
     */
    private BlogSidebarResolver $blogSidebarResolver;

    /**
     * @var BlogCategoryResolver
     */
    private BlogCategoryResolver $blogCategoryResolver;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var BlogAbstractResolver
     */
    private BlogAbstractResolver $abstractResolver;

    /**
     * @param EntityRepository $blogCategoryRepository
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param BlogCategoryResolver $blogCategoryResolver
     * @param SystemConfigService $systemConfigService
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     * @param BlogAbstractResolver $abstractResolver
     */
    public function __construct(
        EntityRepository  $blogCategoryRepository,
        GenericPageLoaderInterface $genericPageLoader,
        BlogSidebarResolver        $blogSidebarResolver,
        BlogCategoryResolver       $blogCategoryResolver,
        SystemConfigService        $systemConfigService,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        BlogAbstractResolver              $abstractResolver
    ) {
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->blogCategoryResolver = $blogCategoryResolver;
        $this->systemConfigService = $systemConfigService;
        $this->seoUrlReplacer = $seoUrlReplacer;
        $this->abstractResolver = $abstractResolver;
    }

    /**
     * @Route("/blog/category/{identifier}", name="frontend.blog.category", methods={"GET"})
     */
    public function blog(string $identifier, Request $request, SalesChannelContext $context): Response
    {
        $category = $this->blogCategoryResolver->getCategory($identifier, $context);

        if (!$category) {
            throw new PageNotFoundException($identifier);
        }

        $categoryPosts = $this->blogCategoryResolver->getPostsByCategory($category, $request, $context);

        $defaultTemplate = $this->systemConfigService->get('MagefanBlog.config.postViewTemplate');
        $template = $category->getPostsListTemplate() ?: $defaultTemplate;
        $pagination = $this->abstractResolver->getPagination($category->blogPosts);
        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $page = $this->genericPageLoader->load($request, $context);

        if ($metaInformation = $page->getMetaInformation()) {
            $pageNumber = $request->query->get('page');
            $pageNumberText = $pageNumber > 1 ? $this->trans('page') . ' ' . $pageNumber . ' - ' : '';

            $metaDescription = $this->systemConfigService->get('MagefanBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('MagefanBlog.config.metaKeywords');

            $metaInformation->setMetaTitle( $pageNumberText . ($category->getMetaTitle() ?: $category->getTitle()));
            $metaInformation->setMetaKeywords($category->getMetaKeywords() ?: $metaKeywords ?: '');
            $metaInformation->setMetaDescription($category->getMetaDescription() ?: $metaDescription ?: '');

            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate(
                'frontend.blog.category',
                ['identifier' => $identifier, 'page' => $pageNumber > 1 ? $pageNumber : null ]
            );
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@MagefanBlog/storefront/page/blog/category/default.html.twig', [
            'page' => $page,
            'category' => $category,
            'sidebar' => $sidebar,
            'categoryPosts' => $categoryPosts,
            'paginationPages' => $pagination
        ]);
    }
}
