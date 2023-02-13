<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\PostTag;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(PostTagEntity $entity)
 * @method void               set(string $key, PostTagEntity $entity)
 * @method PostTagEntity[]    getIterator()
 * @method PostTagEntity[]    getElements()
 * @method PostTagEntity|null get(string $key)
 * @method PostTagEntity|null first()
 * @method PostTagEntity|null last()
 */
class PostTagCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return PostTagEntity::class;
    }
}
