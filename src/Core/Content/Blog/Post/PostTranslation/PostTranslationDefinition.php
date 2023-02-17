<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post\PostTranslation;

use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\System\Language\LanguageDefinition;

class PostTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'magefanblog_post_translation';

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
        return PostTranslationEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return PostTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return PostDefinition::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new ApiAware(), new Required()),

            (new StringField('title', 'title'))->addFlags(new ApiAware(), new Required()),
            (new StringField('meta_title', 'metaTitle'))->addFlags(new ApiAware()),
            (new StringField('meta_keywords', 'metaKeywords'))->addFlags(new ApiAware()),
            (new StringField('meta_description', 'metaDescription'))->addFlags(new ApiAware()),
            (new StringField('content_heading', 'contentHeading'))->addFlags(new ApiAware(), new AllowHtml()),
            (new StringField('content', 'content'))->addFlags(new ApiAware(), new AllowHtml()),
            (new StringField('featured_img', 'featuredImg'))->addFlags(new ApiAware()),
            (new StringField('featured_img_alt', 'featuredImgAlt'))->addFlags(new ApiAware()),
            (new StringField('og_title', 'ogTitle'))->addFlags(new ApiAware()),
            (new StringField('og_description', 'ogDescription'))->addFlags(new ApiAware()),
            (new StringField('og_img', 'ogImg'))->addFlags(new ApiAware()),
            (new StringField('og_type', 'ogType'))->addFlags(new ApiAware()),
            (new StringField('created_at', 'createdAt'))->addFlags(new ApiAware()),
            (new StringField('updated_at', 'updatedAt'))->addFlags(new ApiAware()),
        ]);
    }
}
