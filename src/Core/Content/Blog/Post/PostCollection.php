<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(PostEntity $entity)
 * @method void               set(string $key, PostEntity $entity)
 * @method PostEntity[]    getIterator()
 * @method PostEntity[]    getElements()
 * @method PostEntity|null get(string $key)
 * @method PostEntity|null first()
 * @method PostEntity|null last()
 */
class PostCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return PostEntity::class;
    }
}
