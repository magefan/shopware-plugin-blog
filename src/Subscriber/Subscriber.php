<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Subscriber;

use Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\BlogPostPageSeoUrlRoute;
use Shopware\Core\Content\Seo\Event\SeoEvents;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
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

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCategoryRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $categoryRepository;

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
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param EntityRepositoryInterface $categoryRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SeoUrlUpdater             $seoUrlUpdater,
        EntityRepositoryInterface $blogCategoryRepository,
        EntityRepositoryInterface $categoryRepository,
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
            'magefanblog_post.written' => 'onBlogEntriesUpdated',
            'magefanblog_post.deleted' => 'onBlogPostDeleted',
            'magefanblog_category.written' => 'onBlogEntriesUpdated',
            'magefanblog_category.deleted' => 'onBlogPostDeleted',
            'magefanblog_tag.written' => 'onBlogEntriesUpdated',
            'magefanblog_tag.deleted' => 'onBlogPostDeleted',
            'system_config.written' => 'onSaveConfig',
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

                $blogCategories = $this->blogCategoryRepository->search((new Criteria([])), $context)->getEntities();

                if (!count($blogCategories)) {
                    $criteria = (new Criteria([]))
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