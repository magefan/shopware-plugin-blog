<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\PostCategory;

use Magefan\Blog\Core\Content\Blog\Category\CategoryDefinition;
use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;

class PostCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_post_category';

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
        return PostCategoryEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return PostCategoryCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('created_at', 'createdAt')),
            (new StringField('updated_at', 'updatedAt')),
            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new Required()),
            (new FkField('post_id', 'postId', PostDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('blogPosts', 'post_id', PostDefinition::class, 'id'),
            new ManyToOneAssociationField('blogCategories', 'category_id', CategoryDefinition::class, 'id')
        ]);
    }
}
