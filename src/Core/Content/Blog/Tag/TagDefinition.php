<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Tag;

use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Magefan\Blog\Core\Content\Blog\PostTag\PostTagDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;

class TagDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_tag';

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
        return TagEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return TagCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('title', 'title'))->addFlags(new Required()),
            (new StringField('meta_robots', 'metaRobots')),
            (new StringField('meta_description', 'metaDescription')),
            (new StringField('meta_keywords', 'metaKeywords')),
            (new StringField('meta_title', 'metaTitle')),
            (new LongTextField('content', 'content')),
            (new StringField('identifier', 'identifier')),
            (new StringField('page_layout', 'pageLayout')),
            (new BoolField('is_active', 'isActive')),
            (new StringField('content', 'content')),
            (new StringField('layout_update_xml', 'layoutUpdateXml')),
            (new StringField('custom_theme', 'customTheme')),
            (new StringField('custom_layout', 'customLayout')),
            (new StringField('custom_layout_update_xml', 'customLayoutUpdateXml')),
            (new DateField('custom_theme_from', 'customThemeFrom')),
            (new DateField('custom_theme_to', 'customThemeTo')),
            (new IntField('posts_per_page', 'postsPerPage', 0)),
            (new StringField('posts_list_template', 'postsListTemplate')),
            (new DateField('created_at', 'createdAt')),
            (new DateField('updated_at', 'updatedAt')),
            new ManyToManyAssociationField(
                'postTags',
                PostDefinition::class,
                PostTagDefinition::class,
                'tag_id',
                'post_id'
            ),
        ]);
    }
}
