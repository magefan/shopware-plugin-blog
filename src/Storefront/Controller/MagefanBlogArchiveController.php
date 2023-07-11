<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogAbstractResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogArchiveResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogArchiveController extends StorefrontController
{
    /**
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericPageLoader;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @var BlogSidebarResolver
     */
    private BlogSidebarResolver $blogSidebarResolver;

    /**
     * @var BlogArchiveResolver
     */
    private BlogArchiveResolver $blogArchiveResolver;

    /**
     * @var BlogAbstractResolver
     */
    private BlogAbstractResolver $abstractResolver;

    /**
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param SystemConfigService $systemConfigService
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param BlogArchiveResolver $blogArchiveResolver
     * @param BlogAbstractResolver $abstractResolver
     */
    public function __construct(
        GenericPageLoaderInterface        $genericPageLoader,
        SystemConfigService               $systemConfigService,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        BlogSidebarResolver               $blogSidebarResolver,
        BlogArchiveResolver               $blogArchiveResolver,
        BlogAbstractResolver              $abstractResolver
    )
    {
        $this->genericPageLoader = $genericPageLoader;
        $this->systemConfigService = $systemConfigService;
        $this->seoUrlReplacer = $seoUrlReplacer;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->blogArchiveResolver = $blogArchiveResolver;
        $this->abstractResolver = $abstractResolver;
    }

    /**
     * @Route("/blog/archive/{identifier}", name="frontend.blog.archive", methods={"GET"})
     */
    public function blog(string $identifier, Request $request, SalesChannelContext $context): Response
    {
        $results = $this->blogArchiveResolver->getArchive($identifier, $request, $context);

        if (!$results) {
            throw new PageNotFoundException($identifier);
        }

        $allPosts = $this->blogArchiveResolver->getAllPosts($context);
        $pagination = $this->abstractResolver->getPagination($allPosts);
        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $page = $this->genericPageLoader->load($request, $context);
        $pageNumber = $request->query->get('page');

        if ($metaInformation = $page->getMetaInformation()) {
            $formattedDate = date('M d', strtotime($identifier));
            $pageNumberText = $pageNumber > 1 ? $this->trans('page') . ' ' . $pageNumber . ' - ' : '';
            $metaInformation->setMetaTitle(
                $pageNumberText . $this->trans('archiveTitle') . ' ' . $formattedDate
            );
            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate(
                'frontend.blog.archive',
                ['identifier' => $identifier, 'page' => $pageNumber > 1 ? $pageNumber : null]
            );
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@MagefanBlog/storefront/page/blog/archive/default.html.twig', [
            'page' => $page,
            'posts' => $results,
            'sidebar' => $sidebar,
            'paginationPages' => $pagination
        ]);
    }
}
