<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Subscriber;

use Shopware\Core\Content\Seo\Event\SeoEvents;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    const MENU_SETUP = [
        'MagefanBlog.config.DisplayBlogLink',
        'MagefanBlog.config.LinkText',
        'MagefanBlog.config.IncludeBlogCategories'
    ];

    const ROUTES = [
        '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\BlogCategoryPageSeoUrlRoute',
        '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\BlogPostPageSeoUrlRoute',
        '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\BlogTagPageSeoUrlRoute'
    ];

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogCategoryRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $categoryRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * @var SeoUrlUpdater
     */
    private SeoUrlUpdater $seoUrlUpdater;

    /**
     * @param SeoUrlUpdater $seoUrlUpdater
     * @param EntityRepository $blogCategoryRepository
     * @param EntityRepository $categoryRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SeoUrlUpdater             $seoUrlUpdater,
        EntityRepository $blogCategoryRepository,
        EntityRepository $categoryRepository,
        SystemConfigService       $systemConfigService
    )
    {
        $this->seoUrlUpdater = $seoUrlUpdater;
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'magefanblog_post.written' => [
                ['onBlogEntriesUpdated', 10],
                ['onUpdateInvalidateCache', 11],
            ],
            'magefanblog_post.deleted' => [
                ['onBlogEntriesDeleted', 10],
                ['onDeleteInvalidateCache', 11],
            ],
            'magefanblog_category.written' => [
                ['onBlogEntriesUpdated', 10],
                ['onUpdateInvalidateCache', 11],
            ],
            'magefanblog_category.deleted' => [
                ['onBlogEntriesDeleted', 10],
                ['onDeleteInvalidateCache', 11],
            ],
            'magefanblog_tag.written' => [
                ['onBlogEntriesUpdated', 10],
                ['onUpdateInvalidateCache', 11],
            ],
            'magefanblog_tag.deleted' => [
                ['onBlogEntriesDeleted', 10],
                ['onDeleteInvalidateCache', 11],
            ],
            'system_config.written' => 'onSaveConfig',
            SeoEvents::SEO_URL_TEMPLATE_WRITTEN_EVENT => [
                ['updateSeoUrlForAllPosts', 10],
            ],
        ];
    }

    /**
     * @param EntityWrittenEvent $event
     * @return void
     */
    public function onBlogEntriesUpdated(EntityWrittenEvent $event): void
    {
        $name = ucfirst(explode('_', $event->getEntityName())[1]);
        $className = '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\Blog' . $name . 'PageSeoUrlRoute';

        $this->seoUrlUpdater->update($className::ROUTE_NAME, $event->getIds());
    }

    /**
     * @param EntityDeletedEvent $event
     * @return void
     */
    public function onBlogEntriesDeleted(EntityDeletedEvent $event): void
    {
        $name = ucfirst(explode('_', $event->getEntityName())[1]);
        $className = '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\Blog' . $name . 'PageSeoUrlRoute';

        $this->seoUrlUpdater->update($className::ROUTE_NAME, $event->getIds());
    }

    /**
     * @return void
     */
    public function updateSeoUrlForAllPosts(): void
    {
        foreach (self::ROUTES as $route) {
            $this->seoUrlUpdater->update($route::ROUTE_NAME, []);
        }
    }

    /**
     * @param EntityWrittenEvent $event
     * @return void
     */
    public function onUpdateInvalidateCache(EntityWrittenEvent $event): void
    {

    }

    /**
     * @param EntityDeletedEvent $event
     * @return void
     */
    public function onDeleteInvalidateCache(EntityDeletedEvent $event): void
    {
    }


    /**
     * @param EntityWrittenEvent $entityWrittenEvent
     * @return void
     */
    public function onSaveConfig(EntityWrittenEvent $entityWrittenEvent)
    {
        foreach ($entityWrittenEvent->getWriteResults() as $result) {

            $property = $result->getPayload();
            if (in_array($property['configurationKey'], self::MENU_SETUP)) {

                $context = Context::createDefaultContext();
                $IncludeBlogCategories = (bool)$this->systemConfigService->get('MagefanBlog.config.IncludeBlogCategories');
                $displayBlogLink = (bool)$this->systemConfigService->get('MagefanBlog.config.DisplayBlogLink');
                $mainBlogCategoryText = $this->systemConfigService->get('MagefanBlog.config.LinkText');

                $blogCategories = $this->blogCategoryRepository->search((new Criteria()), $context)->getEntities();

                if (!count($blogCategories)) {
                    $criteria = (new Criteria())
                        ->addFilter(new EqualsFilter('linkType', 'external'))
                        ->addFilter(new EqualsFilter('externalLink', '/blog'));

                    $blogCategoryRoot = $this->categoryRepository->search($criteria, $context)->getEntities()->first();

                    if ($blogCategoryRoot && $blogCategoryRoot->getId()) {
                        $this->categoryRepository->update(
                            [
                                ['id' => $blogCategoryRoot->getId(), 'active' => $displayBlogLink, 'name' => $mainBlogCategoryText]
                            ],
                            $context
                        );
                    }
                }

                if ($blogCategories) {
                    foreach ($blogCategories as $category) {

                        if ($category->getIsActive()) {
                            $this->categoryRepository->update([['id' => $category->getId(), 'active' => $IncludeBlogCategories]], $context);
                        }
                        $child = $this->categoryRepository->search(new Criteria([$category->getId()]), $context)->first();
                        if ($child->getParentId()) {
                            $this->categoryRepository->update(
                                [
                                    ['id' => $child->getParentId(), 'active' => $displayBlogLink, 'name' => $mainBlogCategoryText]
                                ],
                                $context
                            );
                        }
                    }
                }
            }
        }
    }
}