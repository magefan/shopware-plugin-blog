<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Tag;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(TagEntity $entity)
 * @method void               set(string $key, TagEntity $entity)
 * @method TagEntity[]    getIterator()
 * @method TagEntity[]    getElements()
 * @method TagEntity|null get(string $key)
 * @method TagEntity|null first()
 * @method TagEntity|null last()
 */
class TagCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return TagEntity::class;
    }
}
