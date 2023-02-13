<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
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
     * @param EntityRepositoryInterface $blogCategoryRepository
     * @param EntityRepositoryInterface $categoryRepository
     */
    public function __construct(
        EntityRepositoryInterface $blogCategoryRepository,
        EntityRepositoryInterface $categoryRepository,
        SystemConfigService       $systemConfigService

    )
    {
        $this->blogCategoryRepository = $blogCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return ['system_config.written' => 'onSaveConfig',];
    }

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

                if ($blogCategories){
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