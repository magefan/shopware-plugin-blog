<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogListResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogController extends StorefrontController
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericPageLoader;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogPostRepository;

    /**
     * @var BlogListResolver
     */
    private BlogListResolver $blogListResolver;

    /**
     * @var BlogSidebarResolver
     */
    private BlogSidebarResolver $blogSidebarResolver;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @param EntityRepository $blogPostRepository
     * @param SystemConfigService $systemConfigService
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param BlogListResolver $blogListResolver
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     */
    public function __construct(
        EntityRepository                  $blogPostRepository,
        SystemConfigService               $systemConfigService,
        GenericPageLoaderInterface        $genericPageLoader,
        BlogListResolver                  $blogListResolver,
        BlogSidebarResolver               $blogSidebarResolver,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->systemConfigService = $systemConfigService;
        $this->genericPageLoader = $genericPageLoader;
        $this->blogListResolver = $blogListResolver;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->seoUrlReplacer = $seoUrlReplacer;
    }


    public function extra($parameter)
    {
        return new Response($parameter);
    }

    /**
     * @Route("/blog", name="frontend.blog", methods={"GET"})
     */
    public function blog(Request $request, SalesChannelContext $context): Response
    {
        $posts = $this->blogListResolver->getPosts($request, $context);
        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $page = $this->genericPageLoader->load($request, $context);
        $pagination = $this->blogListResolver->getPagination($context);
        $template = $this->systemConfigService->get('MagefanBlog.config.blogIndexTemplate');

        if ($metaInformation = $page->getMetaInformation()) {
            $title = $this->systemConfigService->get('MagefanBlog.config.title');
            $metaTitle = $this->systemConfigService->get('MagefanBlog.config.metaTitle');
            $metaDescription = $this->systemConfigService->get('MagefanBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('MagefanBlog.config.metaKeywords');
            $pageNumber = $request->query->get('page');
            $pageNumberText = $pageNumber > 1 ? $this->trans('page') . ' ' . $pageNumber . ' - ' : '';
            $metaInformation->setMetaTitle($pageNumberText . ($metaTitle ?: $title));
            $metaInformation->setMetaDescription($metaDescription ?: '');
            $metaInformation->setMetaKeywords($metaKeywords ?: '');
            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate(
                'frontend.blog',
                ['page' => $pageNumber > 1 ? $pageNumber : null]
            );
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@MagefanBlog/storefront/page/blog/default.html.twig', [
            'page' => $page,
            'posts' => $posts,
            'sidebar' => $sidebar,
            'paginationPages' => $pagination
        ]);
    }
}
