<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Util;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;

class LifeCycle
{

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $entityRepository;

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var Connection
     */
    private Connection $connection;


    /**
     * @param EntityRepositoryInterface $entityRepository
     * @param RequestStack $requestStack
     * @param Connection $connection
     */
    public function __construct(
        EntityRepositoryInterface $entityRepository,
        RequestStack              $requestStack,
        Connection                $connection
    )
    {
        $this->entityRepository = $entityRepository;
        $this->requestStack = $requestStack;
        $this->connection = $connection;
    }

    /**
     * @param Context $context
     * @return void
     */
    public function install(Context $context): void
    {
        $this->createBlogCategories($context);
    }

    /**
     * @param $context
     * @return void
     */
    public function createBlogCategories($context): void
    {
        $existBlogCategory = $this->checkIfBlogCategoryExist();
        if ($existBlogCategory) {
            return;
        }

        $blogCategories = [];

        $baseUrl = $this->requestStack->getCurrentRequest()->getBaseUrl();
        $rootCategory = $this->getRootCategory($context);
        $lastCategory = $this->getLastCategory($context);

        $blogCategories[0] = [
            "id" => Uuid::randomHex(),
            "type" => "link",
            "name" => 'Blog',
            "linkType" => 'external',
            "externalLink" => $baseUrl . "/blog",
            "level" => 2,
            "active" => true,
        ];

        if ($rootCategory) {
            $blogCategories[0]["parentId"] = $rootCategory->getId();
        }

        if ($lastCategory) {
            $blogCategories[0]["afterCategoryId"] = $lastCategory->getId();
        }

        $this->entityRepository->create(
            $blogCategories
            , Context::createDefaultContext()
        );
    }

    /**
     * @param $context
     * @return mixed|null
     */
    private function getRootCategory($context)
    {
        $categoryRootCriteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('active', 1))
            ->addFilter(new EqualsFilter('parentId', null));

        return $this->entityRepository->search($categoryRootCriteria, $context)->getEntities()->first();
    }

    /**
     * @param $context
     * @return array|mixed|null
     */
    private function getLastCategory($context)
    {
        $categoryCriteria = (new Criteria([]))->addFilter(new EqualsFilter('active', 1));

        return $this->entityRepository->search($categoryCriteria, $context)->getEntities()->last();
    }

    /**
     * @return array
     */
    private function checkIfBlogCategoryExist(): array
    {
        return $this->connection->fetchAll(
            'SELECT category_id, link_type, name FROM category_translation
                 WHERE name = :name AND link_type = :link_type',
            ['name' => 'Blog', 'link_type' => 'external']
        );
    }
}
