<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Twig\Extension;

use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class BlogRuntime implements RuntimeExtensionInterface
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCommentRepository;

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
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $seoUrlRepository;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     * @param EntityRepositoryInterface $blogTagRepository
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param EntityRepositoryInterface $blogCommentRepository
     * @param EntityRepositoryInterface $seoUrlRepository
     * @param SeoUrlPlaceholderHandlerInterface $seoUrlReplacer
     * @param SystemConfigService $systemConfigService
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityRepositoryInterface         $blogPostRepository,
        EntityRepositoryInterface         $blogTagRepository,
        EntityRepositoryInterface         $blogCategoryRepository,
        EntityRepositoryInterface         $blogCommentRepository,
        EntityRepositoryInterface         $seoUrlRepository,
        SeoUrlPlaceholderHandlerInterface $seoUrlReplacer,
        SystemConfigService               $systemConfigService,
        TranslatorInterface               $translator
    )
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->blogTagRepository = $blogTagRepository;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->blogCommentRepository = $blogCommentRepository;
        $this->seoUrlRepository = $seoUrlRepository;
        $this->seoUrlReplacer = $seoUrlReplacer;
        $this->systemConfigService = $systemConfigService;
        $this->translator = $translator;
    }

    /**
     * @param $parentTag
     * @return EntityCollection
     */
    public function getChild($parentTag): EntityCollection
    {
        $criteria = (new Criteria())
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
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('status', 1))
            ->addFilter(new EqualsFilter('parentId', null))
            ->addFilter(new EqualsFilter('postId', $post->getId()));

        return $this->blogCommentRepository->search($criteria, Context::createDefaultContext())->getEntities();
    }

    /**
     * @param $pathInfo
     * @return array
     */
    public function getBreadcrumbsData($pathInfo)
    {
        $path = [];
        $pathItems = explode('/', $pathInfo);
        foreach ($pathItems as $key => $pathItem) {
            if (!strlen($pathItem)) {
                continue;
            }
            $context = Context::createDefaultContext();

            switch ($pathItem) {
                case 'blog':
                    $link = $this->seoUrlReplacer->generate('frontend.blog', ['page' => null]);
                    $title = $this->systemConfigService->get('MagefanBlog.config.title');
                    $path[] = ['name' => $title, 'link' => $link];
                    break;
                case 'post':
                    $id = $pathItems[$key + 1];
                    $postsCriteria = (new Criteria([$id]))->addAssociation('postCategories');
                    $post = $this->blogPostRepository->search($postsCriteria, $context)->getEntities()->first();
                    $category = $post->postCategories->first();

                    $fromCategory = false;
                    if (isset($_SERVER['HTTP_REFERER']) && false !== strpos($_SERVER['HTTP_REFERER'], '/blog/')) {
                        $prevUrl = $_SERVER['HTTP_REFERER'];
                        $seoPath = substr($prevUrl, strpos($prevUrl, '/blog/') + 1);
                        $seoUrlCriteria = (new Criteria([]))->addFilter(new EqualsFilter('seoPathInfo',$seoPath));
                        $seoUrl = $this->seoUrlRepository->search($seoUrlCriteria, $context)->getEntities()->first();
                        if($seoUrl && $seoUrl->getId()){
                            $fromCategory = $this->blogCategoryRepository->search((new Criteria([$seoUrl->getForeignKey()])), $context)->getEntities()->first();
                        }
                    }

                    $category = $fromCategory ?: $category;

                    if ($category && $category->getId()){
                        $link = $this->seoUrlReplacer->generate('frontend.blog.category', ['identifier' => $category->getId(), 'page' => null]);
                        $path[] = ['name' => $category->getTitle(), 'link' => $link];
                    }

                    $link = $this->seoUrlReplacer->generate('frontend.blog.post', ['identifier' => $post->getId(), 'page' => null]);
                    $path[] = ['name' => $post->getTitle(), 'link' => $link];
                    break;
                case 'category':
                    $id = $pathItems[$key + 1];
                    $category = $this->blogCategoryRepository->search(new Criteria([$id]), $context)->getEntities()->first();
                    $link = $this->seoUrlReplacer->generate('frontend.blog.category', ['identifier' => $id, 'page' => null]);
                    $path[] = ['name' => $category->getTitle(), 'link' => $link];
                    break;
                case 'tag':
                    $id = $pathItems[$key + 1];
                    $category = $this->blogTagRepository->search(new Criteria([$id]), $context)->getEntities()->first();
                    $link = $this->seoUrlReplacer->generate(
                        'frontend.blog.tag',
                        ['identifier' => $id, 'page' => null]
                    );
                    $path[] = ['name' => $category->getTitle(), 'link' => $link];
                    break;
                case 'archive':
                    $name = $pathItems[$key + 1];
                    $link = $this->seoUrlReplacer->generate('frontend.blog.archive', ['identifier' => $name, 'page' => null]);
                    $path[] = ['name' => $this->translator->trans('Monthly Archives:') . ' ' . $name, 'link' => $link];
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