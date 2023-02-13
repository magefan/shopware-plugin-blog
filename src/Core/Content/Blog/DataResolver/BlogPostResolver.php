<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

class BlogPostResolver
{
    protected $_prevPost;

    protected $_nextPost;

    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $blogPostRepository;

    /**
     * @param EntityRepositoryInterface $blogPostRepository
     */
    public function __construct(
        EntityRepositoryInterface $blogPostRepository
    ) {
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * @param $identifier
     * @param $context
     * @return mixed|null
     */
    public function getPost($identifier, $context)
    {
        $postCriteria = (new Criteria([]))
            ->addFilter(new EqualsFilter('identifier', $identifier))
            ->addFilter(new EqualsFilter('isActive', 1))
            ->addAssociation('postCategories')
            ->addAssociation('postTags')
            ->addAssociation('media')
            ->addAssociation('postAuthor')
            ->addAssociation('postComments');

        $postCriteria->getAssociation('postComments')
            ->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        return $this->blogPostRepository->search($postCriteria, $context->getContext())->getEntities()->first();
    }

    /***
     * @param $post
     * @param $context
     * @return array
     */
    public function getPrevNext($post, $context)
    {
        $prev = $this->getPrevPost($post, $context);
        $next = $this->getNextPost($post, $context);
        return ['prev' => $prev, 'next' => $next];
    }

    /**
     * @param $currentPost
     * @param $context
     * @return array|false|mixed|null
     */
    public function getPrevPost($currentPost, $context)
    {
        if ($this->_prevPost === null) {
            $this->_prevPost = false;

            $postCriteria = (new Criteria([]))
                ->addFilter(new EqualsFilter('isActive', 1))
                ->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING))
                ->addFilter(
                    new RangeFilter('createdAt', [
                        RangeFilter::LT => $currentPost->createdAt->format(Defaults::STORAGE_DATE_TIME_FORMAT)
                    ])
                );

            $this->_prevPost = $this->blogPostRepository->search($postCriteria, $context->getContext())->getEntities()->first();

            if (!$this->_prevPost || $currentPost->id === $this->_prevPost->id) {
                return false;
            }
        }

        return $this->_prevPost;
    }

    /**
     * @param $currentPost
     * @param $context
     * @return array|false|mixed|null
     */
    public function getNextPost($currentPost, $context)
    {
        if ($this->_nextPost === null) {
            $this->_nextPost = false;

            $postCriteria = (new Criteria([]))
                ->addFilter(new EqualsFilter('isActive', 1))
                ->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING))
                ->addFilter(
                    new RangeFilter('createdAt', [
                        RangeFilter::GT => $currentPost->createdAt->format(Defaults::STORAGE_DATE_TIME_FORMAT)
                    ])
                );

            $this->_nextPost = $this->blogPostRepository->search($postCriteria, $context->getContext())->getEntities()->first();

            if (!$this->_nextPost || $currentPost->id === $this->_nextPost->id) {
                return false;
            }
        }
        return $this->_nextPost;
    }
}
