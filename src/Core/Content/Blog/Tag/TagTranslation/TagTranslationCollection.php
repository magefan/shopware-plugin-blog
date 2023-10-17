<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Tag\TagTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(TagTranslationEntity $entity)
 * @method void               set(string $key, TagTranslationEntity $entity)
 * @method TagTranslationEntity[]    getIterator()
 * @method TagTranslationEntity[]    getElements()
 * @method TagTranslationEntity|null get(string $key)
 * @method TagTranslationEntity|null first()
 * @method TagTranslationEntity|null last()
 */
class TagTranslationCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return TagTranslationEntity::class;
    }
}
