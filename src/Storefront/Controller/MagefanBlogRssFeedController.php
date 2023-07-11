<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogRssFeedController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private $blogPostRepository;

    /**
     * @var GenericPageLoaderInterface
     */
    private $genericPageLoader;

    /**
     * @var BlogSidebarResolver
     */
    private $blogSidebarResolver;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * MagefanBlogRssFeedController constructor.
     * @param EntityRepository $blogPostRepository
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepository $blogPostRepository,
        GenericPageLoaderInterface $genericPageLoader,
        BlogSidebarResolver $blogSidebarResolver,
        SystemConfigService $systemConfigService
    ) {
        $this->blogPostRepository = $blogPostRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/blog/rss", name="frontend.blog.rss", methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
    public function blog(Request $request, SalesChannelContext $context): Response
    {
        $isEnabled = $this->systemConfigService->get('MagefanBlog.config.rssSidebarEnabled');
        if (!$isEnabled) {
            throw new PageNotFoundException('rss');
        }

        $rssFeedConfig = [
            'title' => $this->systemConfigService->get('MagefanBlog.config.rssSidebarFeedTitle'),
            'description' => $this->systemConfigService->get('MagefanBlog.config.rssSidebarFeedDescription')
        ];

        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $criteria = (new Criteria())->addFilter(new EqualsFilter('isActive', 1));
        $results = $this->blogPostRepository->search($criteria, $context->getContext())->getEntities();
        $page = $this->genericPageLoader->load($request, $context);
        $response = $this->renderStorefront('@MagefanBlog/storefront/page/blog/rss/default.xml.twig', [
            'page' => $page,
            'posts' => $results,
            'sidebar' => $sidebar,
            'config' => $rssFeedConfig
        ]);
        $response->headers->set('content-type', 'text/xml; charset=utf-8');

        return  $response;
    }
}
