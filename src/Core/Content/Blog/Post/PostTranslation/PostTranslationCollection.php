<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post\PostTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(PostTranslationEntity $entity)
 * @method void               set(string $key, PostTranslationEntity $entity)
 * @method PostTranslationEntity[]    getIterator()
 * @method PostTranslationEntity[]    getElements()
 * @method PostTranslationEntity|null get(string $key)
 * @method PostTranslationEntity|null first()
 * @method PostTranslationEntity|null last()
 */
class PostTranslationCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return PostTranslationEntity::class;
    }
}
