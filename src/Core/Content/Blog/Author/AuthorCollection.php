<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Author;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(AuthorEntity $entity)
 * @method void               set(string $key, AuthorEntity $entity)
 * @method AuthorEntity[]    getIterator()
 * @method AuthorEntity[]    getElements()
 * @method AuthorEntity|null get(string $key)
 * @method AuthorEntity|null first()
 * @method AuthorEntity|null last()
 */
class AuthorCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return AuthorEntity::class;
    }
}
