<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Category;

use Magefan\Blog\Core\Content\Blog\Category\CategoryTranslation\CategoryTranslationDefinition;
use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Magefan\Blog\Core\Content\Blog\PostCategory\PostCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;

class CategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_category';

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
        return CategoryEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return CategoryCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),

            //translations
            (new TranslatedField('title'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaKeywords'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('contentHeading'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('content'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('path'))->addFlags(new ApiAware(), new Inherited()),

            (new StringField('identifier', 'identifier')),
            (new IntField('position', 'position')),
            (new StringField('posts_sort_by', 'postsSortBy')),
            (new BoolField('include_in_menu', 'includeInMenu')),
            (new BoolField('is_active', 'isActive')),
            (new IntField('display_mode', 'displayMode')),
            (new StringField('page_layout', 'pageLayout')),
            (new StringField('layout_update_xml', 'layoutUpdateXml')),
            (new StringField('custom_theme', 'customTheme')),
            (new StringField('custom_layout', 'customLayout')),
            (new StringField('custom_layout_update_xml', 'customLayoutUpdateXml')),
            (new DateField('custom_theme_from', 'customThemeFrom')),
            (new DateField('custom_theme_to', 'customThemeTo')),
            (new IntField('posts_per_page', 'postsPerPage')),
            (new StringField('posts_list_template', 'postsListTemplate')),
            (new StringField('created_at', 'createdAt')),
            (new StringField('updated_at', 'updatedAt')),

            // associations
            (new OneToOneAssociationField('blogCategories', 'id', 'category_id', PostCategoryDefinition::class, false))->addFlags(new CascadeDelete()), new ManyToManyAssociationField('blogPosts', PostDefinition::class, PostCategoryDefinition::class, 'category_id', 'post_id'),
            (new TranslationsAssociationField(CategoryTranslationDefinition::class, 'magefanblog_category_id'))->addFlags(new ApiAware(), new Required())
        ]);
    }
}
