<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\PostCategory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(PostCategoryEntity $entity)
 * @method void               set(string $key, PostCategoryEntity $entity)
 * @method PostCategoryEntity[]    getIterator()
 * @method PostCategoryEntity[]    getElements()
 * @method PostCategoryEntity|null get(string $key)
 * @method PostCategoryEntity|null first()
 * @method PostCategoryEntity|null last()
 */
class PostCategoryCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return PostCategoryEntity::class;
    }
}
