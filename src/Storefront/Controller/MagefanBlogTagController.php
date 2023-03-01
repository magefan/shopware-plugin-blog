<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Storefront\Controller;

use Doctrine\DBAL\Exception;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogAbstractResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogPostResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogTagResolver;
use Shopware\Core\Content\Cms\Exception\PageNotFoundException;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class MagefanBlogTagController extends StorefrontController
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogTagRepository;

    /**
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericPageLoader;

    /**
     * @var BlogPostResolver
     */
    private BlogPostResolver $blogPostResolver;

    /**
     * @var BlogSidebarResolver
     */
    private BlogSidebarResolver $blogSidebarResolver;

    /**
     * @var BlogTagResolver
     */
    private BlogTagResolver $blogTagResolver;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @var BlogAbstractResolver
     */
    private BlogAbstractResolver $abstractResolver;

    /**
     * @param BlogTagResolver $blogTagResolver
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param SystemConfigService $systemConfigService
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     * @param BlogAbstractResolver $abstractResolver
     */
    public function __construct(
        BlogTagResolver            $blogTagResolver,
        GenericPageLoaderInterface $genericPageLoader,
        BlogSidebarResolver        $blogSidebarResolver,
        SystemConfigService        $systemConfigService,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        BlogAbstractResolver              $abstractResolver
    ) {
        $this->blogTagResolver = $blogTagResolver;
        $this->genericPageLoader = $genericPageLoader;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->systemConfigService = $systemConfigService;
        $this->seoUrlReplacer = $seoUrlReplacer;
        $this->abstractResolver = $abstractResolver;
    }

    /**
     * @Route("/blog/tag/{identifier}", name="frontend.blog.tag", methods={"GET"})
     * @throws Exception
     */
    public function blog(string $identifier, Request $request, SalesChannelContext $context): Response
    {
        $tag = $this->blogTagResolver->getTag($identifier, $context);

        if (!$tag) {
            throw new PageNotFoundException($identifier);
        }

        $tagPosts = $this->blogTagResolver->getPostsByTag($tag, $request, $context);
        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $pagination = $this->abstractResolver->getPagination($tag->postTags);
        $template = $tag->getPostsListTemplate() ?: 'default';

        $page = $this->genericPageLoader->load($request, $context);

        if ($metaInformation = $page->getMetaInformation()) {
            $pageNumber = $request->query->get('page');
            $pageNumberText = $pageNumber > 1 ? $this->trans('page') . ' ' . $pageNumber . ' - ' : '';

            $metaDescription = $this->systemConfigService->get('MagefanBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('MagefanBlog.config.metaKeywords');

            $metaInformation->setMetaTitle($pageNumberText . ($tag->getMetaTitle() ?: $tag->getTitle()));
            $metaInformation->setMetaKeywords($tag->getMetaKeywords() ?: $metaKeywords ?: '');
            $metaInformation->setMetaDescription($tag->getMetaDescription() ?: $metaDescription ?: '');

            $defaultRobots = $this->systemConfigService->get('MagefanBlog.config.defaultTagRobots');
            $formattedDefaultRobots = strtoupper(implode(', ',preg_split('/(?=[A-Z])/', $defaultRobots, -1, PREG_SPLIT_NO_EMPTY)));
            $tagRobots = $tag->getMetaRobots();
            $metaInformation->setRobots($tagRobots == 'config' ? $formattedDefaultRobots : $tagRobots);

            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate(
                'frontend.blog.tag',
                ['identifier' => $identifier, 'page' => $pageNumber > 1 ? $pageNumber : null ]
            );
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@MagefanBlog/storefront/page/blog/tag/default.html.twig', [
            'tag' => $tag,
            'tagPosts' => $tagPosts,
            'page' => $page,
            'sidebar' => $sidebar,
            'paginationPages' => $pagination
        ]);
    }
}
