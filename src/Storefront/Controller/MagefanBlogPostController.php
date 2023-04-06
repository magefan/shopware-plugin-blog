<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogPostResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
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
class MagefanBlogPostController extends StorefrontController
{
    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogAuthorRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogTagRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCategoryRepository;

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
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     * @param EntityRepositoryInterface $blogAuthorRepository
     * @param EntityRepositoryInterface $blogTagRepository
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param BlogPostResolver $blogPostResolver
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param SystemConfigService $systemConfigService
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     */
    public function __construct(
        EntityRepositoryInterface  $blogPostRepository,
        EntityRepositoryInterface  $blogAuthorRepository,
        EntityRepositoryInterface  $blogTagRepository,
        EntityRepositoryInterface  $blogCategoryRepository,
        GenericPageLoaderInterface $genericPageLoader,
        BlogPostResolver           $blogPostResolver,
        BlogSidebarResolver $blogSidebarResolver,
        SystemConfigService        $systemConfigService,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
    ) {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogAuthorRepository = $blogAuthorRepository;
        $this->blogTagRepository = $blogTagRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->genericPageLoader = $genericPageLoader;
        $this->blogPostResolver = $blogPostResolver;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->systemConfigService = $systemConfigService;
        $this->seoUrlReplacer = $seoUrlReplacer;
    }

    /**
     * @Route("/blog/post/{identifier}", name="frontend.blog.post", methods={"GET"})
     */
    public function blog(string $identifier, Request $request, SalesChannelContext $context): Response
    {
        $post = $this->blogPostResolver->getPost($identifier, $context);

        if (!$post) {
            throw new PageNotFoundException($identifier);
        }

        $prevNext = $this->blogPostResolver->getPrevNext($post, $context);

        $defaultTemplate = $this->systemConfigService->get('MagefanBlog.config.postViewTemplate');
        $template = $defaultTemplate ?? 'default';

        $sidebar = $this->blogSidebarResolver->getSidebar($context);
        $page = $this->genericPageLoader->load($request, $context);

        if ($metaInformation = $page->getMetaInformation()) {

            $metaDescription = $this->systemConfigService->get('MagefanBlog.config.metaDescription');
            $metaKeywords = $this->systemConfigService->get('MagefanBlog.config.metaKeywords');

            $metaInformation->setMetaTitle($post->getMetaTitle() ?: $post->getTitle() ?: '');
            $metaInformation->setMetaKeywords($post->getMetaKeywords() ?: $metaKeywords ?: '');
            $metaInformation->setMetaDescription($post->getMetaDescription() ?: $metaDescription ?: '');

            if ($post->postAuthor) {
                $metaInformation->setAuthor($post->postAuthor->firstname . ' ' . $post->postAuthor->lastname);
            }

            $page->setMetaInformation($metaInformation);

            $canonical = $this->seoUrlReplacer->generate('frontend.blog.post', ['identifier' => $identifier]);
            $page->getMetaInformation()->setCanonical($canonical);
        }

        return $this->renderStorefront('@MagefanBlog/storefront/page/blog/post/'. $template .'.html.twig', [
            'page' => $page,
            'post' => $post,
            'sidebar' => $sidebar,
            'prevnext' => $prevNext,
        ]);
    }
}
