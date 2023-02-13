<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Comment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(CommentEntity $entity)
 * @method void               set(string $key, CommentEntity $entity)
 * @method CommentEntity[]    getIterator()
 * @method CommentEntity[]    getElements()
 * @method CommentEntity|null get(string $key)
 * @method CommentEntity|null first()
 * @method CommentEntity|null last()
 */
class CommentCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return CommentEntity::class;
    }
}
