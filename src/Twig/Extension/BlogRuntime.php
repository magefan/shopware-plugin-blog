<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Twig\Extension;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Twig\Extension\RuntimeExtensionInterface;

class BlogRuntime implements RuntimeExtensionInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogCommentRepository;

    /**
     * @param EntityRepositoryInterface $blogCommentRepository
     */
    public function __construct(
        EntityRepositoryInterface $blogCommentRepository
    )
    {
        $this->blogCommentRepository = $blogCommentRepository;
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
}