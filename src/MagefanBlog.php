<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\SuffixFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Magefan\Blog\Util\LifeCycle;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Kernel;

class MagefanBlog extends Plugin
{

    /**
     * @param InstallContext $installContext
     * @return void
     */
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->createBlogMediaFolder($installContext->getContext());
        $this->getLifeCycle()->install($installContext->getContext());
    }

    /**
     * @param UninstallContext $context
     * @return void
     */
    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        if ($context->keepUserData()) {
            return;
        }

        $this->deleteMediaFolder($context->getContext());
        $this->deleteDefaultMediaFolder($context->getContext());

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_author`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_category`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_category_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_comment`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_post`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_post_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_post_category`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_post_tag`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_tag`');
        $connection->executeStatement('DROP TABLE IF EXISTS `magefanblog_tag_translation`');
        $connection->executeStatement("DELETE FROM seo_url_template WHERE entity_name LIKE 'magefanblog_%';");
        $connection->executeStatement("DELETE FROM seo_url WHERE route_name LIKE 'frontend.blog%';");
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @param ActivateContext $activateContext
     * @return void
     * @throws Exception
     */
    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $categoryRepository = $this->container->get('category.repository');

        $context = Context::createDefaultContext();
        $connection = Kernel::getConnection();

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('linkType', 'external'))
            ->addFilter(new SuffixFilter('externalLink', '/blog'))
            ->addAssociation('children');

        $blogCategoryRoot = $categoryRepository->search($criteria, $context)->getEntities()->first();

        if ($blogCategoryRoot && $blogCategoryRoot->getId()) {
            $categoryRepository->update([['id' => $blogCategoryRoot->getId(), 'active' => true]], $context);

            if ($blogCategories = $blogCategoryRoot->getChildren()) {
                foreach ($blogCategories as $blogCategory) {

                    if ($blogCategory) {
                        $status = $connection->executeQuery(
                            'SELECT HEX(id),`include_in_menu`,`is_active` FROM `magefanblog_category` WHERE id = :id',
                            ['id' => Uuid::fromHexToBytes($blogCategory->getId())]
                        )->fetchAll();
                        if (isset($status[0])) {
                            $categoryRepository->update(
                                [['id' => $blogCategory->getId(), 'active' => (bool)($status[0]['include_in_menu'] && $status[0]['is_active'])]]
                                , $context
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @param DeactivateContext $deactivateContext
     * @return void
     */
    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);

        $categoryRepository = $this->container->get('category.repository');
        $blogCategoryRepository = $this->container->get('magefanblog_category.repository');

        $context = Context::createDefaultContext();

        $blogCategories = $blogCategoryRepository->search((new Criteria()), $context)->getEntities();

        if (!count($blogCategories)) {
            $criteria = (new Criteria())
                ->addFilter(new EqualsFilter('linkType', 'external'))
                ->addFilter(new EqualsFilter('externalLink', '/blog'));

            $blogCategoryRoot = $categoryRepository->search($criteria, $context)->getEntities()->first();
            if ($blogCategoryRoot && $blogCategoryRoot->getId()) {
                $categoryRepository->update([['id' => $blogCategoryRoot->getId(), 'active' => false]], $context);

            }
        }

        if ($blogCategories) {
            foreach ($blogCategories as $blogCategory) {
                $categoryRepository->update([['id' => $blogCategory->getId(), 'active' => false]], $context);
                $child = $categoryRepository->search(new Criteria([$blogCategory->getId()]), $context)->first();
                if ($child->getParentId()) {
                    $categoryRepository->update([['id' => $child->getParentId(), 'active' => false]], $context);
                }
            }
        }
    }

    /**
     * @param Context $context
     * @return void
     */
    public function createBlogMediaFolder(Context $context): void
    {
        $this->deleteDefaultMediaFolder($context);
        $thumbnailSizes = $this->getThumbnailSizes($context);

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_default_folder.repository');

        $data = [
            [
                'entity' => PostDefinition::ENTITY_NAME,
                'associationFields' => ['media'],
                'folder' => [
                    'name' => 'Magefan Blog Images',
                    'useParentConfiguration' => false,
                    'configuration' => [
                        'createThumbnails' => true,
                        'keepAspectRatio' => true,
                        'thumbnailQuality' => 90,
                        'mediaThumbnailSizes' => $thumbnailSizes,
                    ],
                ],
            ],
        ];

        $mediaFolderRepository->create($data, $context);
    }

    /**
     * @param Context $context
     * @return void
     */
    private function deleteDefaultMediaFolder(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('entity', [PostDefinition::ENTITY_NAME]));

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_default_folder.repository');

        $mediaFolderIds = $mediaFolderRepository->searchIds($criteria, $context)->getIds();

        if (!empty($mediaFolderIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $mediaFolderIds);
            $mediaFolderRepository->delete($ids, $context);
        }
    }

    /**
     * @param Context $context
     * @return void
     */
    private function deleteMediaFolder(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('name', 'Magefan Blog Images')
        );

        /** @var EntityRepository $mediaFolderRepository */
        $mediaFolderRepository = $this->container->get('media_folder.repository');

        $mediaFolderRepository->search($criteria, $context);

        $mediaFolderIds = $mediaFolderRepository->searchIds($criteria, $context)->getIds();

        if (!empty($mediaFolderIds)) {
            $ids = array_map(static function ($id) {
                return ['id' => $id];
            }, $mediaFolderIds
            );
            $mediaFolderRepository->delete($ids, $context);
        }
    }

    /**
     * @param Context $context
     * @return array
     */
    private function getThumbnailSizes(Context $context): array
    {
        $mediaThumbnailSizes = [
            '330x185' => [
                'width' => 330,
                'height' => 185,
            ],
            '650x365' => [
                'width' => 650,
                'height' => 365,
            ],
            '900x506' => [
                'width' => 900,
                'height' => 506,
            ],
            '1280x720' => [
                'width' => 1280,
                'height' => 720,
            ],
        ];

        $criteria = new Criteria();

        /** @var EntityRepository $thumbnailSizeRepository */
        $thumbnailSizeRepository = $this->container->get('media_thumbnail_size.repository');

        $thumbnailSizes = $thumbnailSizeRepository->search($criteria, $context)->getEntities();

        $mediaThumbnailSizesAddedIds = [];
        /** @var MediaThumbnailSizeEntity $thumbnailSize */
        foreach ($thumbnailSizes as $thumbnailSize) {
            $key = $thumbnailSize->getWidth() . 'x' . $thumbnailSize->getHeight();
            if (\array_key_exists($key, $mediaThumbnailSizes)) {
                $mediaThumbnailSize = $mediaThumbnailSizes[$key];
                $mediaThumbnailSizesAddedIds[$key] = array_merge(
                    ['id' => $thumbnailSize->getId()],
                    $mediaThumbnailSize,
                );
                unset($mediaThumbnailSizes[$key]);
            }
        }

        $mediaThumbnailSizesCreateData = [];
        foreach ($mediaThumbnailSizes as $key => $mediaThumbnailSize) {
            $data = array_merge(
                ['id' => Uuid::randomHex()],
                $mediaThumbnailSize,
            );

            $mediaThumbnailSizesCreateData[$key] = $data;
            $mediaThumbnailSizesAddedIds[$key] = $data;
        }

        if (\count($mediaThumbnailSizesCreateData) > 0) {
            $thumbnailSizeRepository->create(array_values($mediaThumbnailSizesCreateData), $context);
        }

        return array_values($mediaThumbnailSizesAddedIds);
    }

    /**
     * @param UpdateContext $updateContext
     * @return void
     */
    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
    }

    /**
     * @return LifeCycle
     */
    public function getLifeCycle(): LifeCycle
    {
        $repository = $this->container->get('category.repository');
        $requestStack = $this->container->get('request_stack');
        $connection = $this->container->get(Connection::class);

        return new LifeCycle(
            $repository,
            $requestStack,
            $connection
        );
    }
}
