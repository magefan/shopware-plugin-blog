<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\DataResolver\BlogAbstractResolver;
use Magefan\Blog\Core\Content\Blog\DataResolver\BlogSidebarResolver;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogSearchController extends StorefrontController
{
    const SEARCHED_FIELDS = [
        'Post' => false,
        'Category' => 'blogPosts',
        'Tag' => 'postTags',
        'Author' => 'authorPosts',
        'Comment' => 'post'
    ];

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogPostRepository;

    /**
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericPageLoader;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogTagRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCategoryRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogAuthorRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCommentRepository;

    /**
     * @var BlogAbstractResolver
     */
    private BlogAbstractResolver $abstractResolver;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    private RouterInterface $router;

    /**
     * @var BlogSidebarResolver
     */
    private BlogSidebarResolver $blogSidebarResolver;

    /**
     * @param EntityRepository $blogPostRepository
     * @param EntityRepository $blogTagRepository
     * @param EntityRepository $blogCategoryRepository
     * @param EntityRepository $blogAuthorRepository
     * @param EntityRepository $blogCommentRepository
     * @param BlogAbstractResolver $abstractResolver
     * @param BlogSidebarResolver $blogSidebarResolver
     * @param GenericPageLoaderInterface $genericPageLoader
     * @param SystemConfigService $systemConfigService
     * @param RouterInterface $router
     */
    public function __construct(
        EntityRepository  $blogPostRepository,
        EntityRepository  $blogTagRepository,
        EntityRepository  $blogCategoryRepository,
        EntityRepository  $blogAuthorRepository,
        EntityRepository  $blogCommentRepository,
        BlogAbstractResolver       $abstractResolver,
        BlogSidebarResolver        $blogSidebarResolver,
        GenericPageLoaderInterface $genericPageLoader,
        SystemConfigService        $systemConfigService,
        RouterInterface            $router
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogTagRepository = $blogTagRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->blogAuthorRepository = $blogAuthorRepository;
        $this->blogCommentRepository = $blogCommentRepository;
        $this->abstractResolver = $abstractResolver;
        $this->blogSidebarResolver = $blogSidebarResolver;
        $this->genericPageLoader = $genericPageLoader;
        $this->systemConfigService = $systemConfigService;
        $this->router = $router;
    }

    /**
     * @Route("/blog/search", name="frontend.blog.search", methods={"GET"})
     */
    public function search(Request $request): Response
    {
        $search = (string)$request->query->get('q');
        if (!$request->query->has('q')) {
            throw new MissingRequestParameterException('q');
        }

        return new RedirectResponse(
            $this->router->generate(
                'frontend.blog.search.result',
                ['search' => $search]
            )
        );
    }

    /**
     * @Route("/blog/search/{search}", name="frontend.blog.search.result", methods={"GET"})
     */
    public function searchResult($search, Request $request, SalesChannelContext $context): Response
    {
        if (!$search) {
            throw new MissingRequestParameterException('search');
        }

        $postIds = [];
        $page = $this->genericPageLoader->load($request, $context);
        $sidebar = $this->blogSidebarResolver->getSidebar($context);

        foreach (self::SEARCHED_FIELDS as $entityName => $associationName) {
            $criteria = (new Criteria());
            $repository = 'blog' . $entityName . 'Repository';

            if ($associationName) {

                $criteria->addAssociation($associationName);
                $criteria->setTerm($search);

                $elements = $this->{$repository}->search($criteria, $context->getContext())->getElements();
                foreach ($elements as $element) {

                    if (!$associate = $element->{$associationName}) {
                        continue;
                    }

                    if (method_exists($associate, 'getElements')) {
                        $postIds = array_merge(array_keys($element->{$associationName}->getElements()), $postIds);
                    } else if (strpos($entityName, 'Comment') !== false) {
                        $postIds = array_merge([$element->getPostId()], $postIds);
                    }
                }
            } else {
                $criteria->setTerm($search);
                $postIds = array_merge($this->{$repository}->search($criteria, $context->getContext())->getIds(), $postIds);
            }
        }

        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('noindex,follow');
        }

        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $pageNumber = $this->abstractResolver->getPage($request);
        $pageOffset = ($pageNumber - 1) * $limit;

        $posts = [];
        if (count($postIds)) {
            $criteria = (new Criteria(array_unique($postIds)))
                ->setLimit($limit)
                ->setOffset($pageOffset);

            $posts = $this->blogPostRepository->search($criteria, $context->getContext());
        }

        $pagination = $this->abstractResolver->getPagination(array_unique($postIds));

        return $this->renderStorefront('@MagefanBlog/storefront/layout/search.html.twig', [
            'page' => $page,
            'query' => $search,
            'posts' => $posts,
            'paginationPages' => $pagination,
            'sidebar' => $sidebar
        ]);
    }
}