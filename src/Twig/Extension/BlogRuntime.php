<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Twig\Extension;

use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Twig\Extension\RuntimeExtensionInterface;

class BlogRuntime implements RuntimeExtensionInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCommentRepository;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private SeoUrlPlaceholderHandlerInterface $seoUrlReplacer;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogTagRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCategoryRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     * @param EntityRepositoryInterface $blogTagRepository
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param EntityRepositoryInterface $blogCommentRepository
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepositoryInterface $blogPostRepository,
        EntityRepositoryInterface $blogTagRepository,
        EntityRepositoryInterface $blogCategoryRepository,
        EntityRepositoryInterface $blogCommentRepository,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        SystemConfigService       $systemConfigService
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogTagRepository = $blogTagRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->blogCommentRepository = $blogCommentRepository;
        $this->seoUrlReplacer = $seoUrlReplacer;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @param $parentTag
     * @return EntityCollection
     */
    public function getChild($parentTag): EntityCollection
    {
        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('status', 1))
            ->addFilter(new EqualsFilter('parentId', $parentTag->getId()));

        return $this->blogCommentRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }

    /**
     * @param $post
     * @return EntityCollection
     */
    public function getPostComments($post): EntityCollection
    {
        $criteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('status', 1))
            ->addFilter(new EqualsFilter('parentId', null))
            ->addFilter(new EqualsFilter('postId', $post->getId()));

        return $this->blogCommentRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }

    /**
     * @param $pathInfo
     * @return array
     */
    public function getBreadcrumbsData($pathInfo) {
        $path = [];
        $pathItems = explode('/', $pathInfo);
        foreach ($pathItems as $key => $pathItem){
            if (!strlen($pathItem)){
                continue;
            }
            switch ($pathItem) {
                case 'blog':
                    $link = $this->seoUrlReplacer->generate('frontend.blog', ['page' => null]);
                    $title = $this->systemConfigService->get('MagefanBlog.config.title');
                    $path[] = ['name' => $title, 'link' => $link];
                    break;
                case 'post':
                    $id = $pathItems[$key + 1];
                    $post = $this->blogPostRepository->search(new Criteria([$id]), Context::createDefaultContext())->getEntities()->first();
                    $link = $this->seoUrlReplacer->generate('frontend.blog.post', ['identifier' => $id, 'page' => null]);
                    $path[] = ['name' => $post->getTitle(), 'link' => $link];
                    break;
                case 'category':
                    $id = $pathItems[$key + 1];
                    $category = $this->blogCategoryRepository->search(new Criteria([$id]), Context::createDefaultContext())->getEntities()->first();
                    $link = $this->seoUrlReplacer->generate('frontend.blog.category', ['identifier' => $id, 'page' => null]);
                    $path[] = ['name' => $category->getTitle(), 'link' => $link];
                    break;
                case 'tag':
                    $id = $pathItems[$key + 1];
                    $category = $this->blogTagRepository->search(new Criteria([$id]), Context::createDefaultContext())->getEntities()->first();
                    $link = $this->seoUrlReplacer->generate(
                        'frontend.blog.tag',
                        ['identifier' => $id, 'page' => null]
                    );
                    $path[] = ['name' => $category->getTitle(), 'link' => $link];
                    break;
                case 'archive':
                    $name = $pathItems[$key + 1];
                    $link = $this->seoUrlReplacer->generate('frontend.blog.archive', ['page' => null]);
                    $path[] = ['name' => $name, 'link' => $link];
                    break;
                case 'search':
                    $term = $pathItems[$key + 1];
                    $link = $this->seoUrlReplacer->generate('frontend.blog.search.result', ['search' => $term, 'page' => null]);
                    $path[] = ['name' => $term, 'link' => $link];
                    break;
            }
        }

        return $path;
    }
}