<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Tag\TagTranslation;

use Magefan\Blog\Core\Content\Blog\Tag\TagDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\System\Language\LanguageDefinition;

class TagTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'magefanblog_tag_translation';

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return TagTranslationEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return TagTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return TagDefinition::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new StringField('meta_robots', 'metaRobots')),
            (new StringField('meta_description', 'metaDescription')),
            (new StringField('meta_keywords', 'metaKeywords')),
            (new StringField('meta_title', 'metaTitle')),
            (new LongTextField('content', 'content')),
            (new StringField('identifier', 'identifier')),
            (new StringField('created_at', 'createdAt')),
            (new StringField('updated_at', 'updatedAt')),
        ]);
    }
}
