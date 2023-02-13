<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\PostTag;

use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Magefan\Blog\Core\Content\Blog\Tag\TagDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;

class PostTagDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_post_tag';

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
        return PostTagEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return PostTagCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FkField('tag_id', 'tagId', TagDefinition::class))->addFlags(new Required()),
            (new DateField('created_at', 'createdAt')),
            (new DateField('updated_at', 'updatedAt')),
            (new FkField('post_id', 'postId', PostDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('tagPosts', 'post_id', PostDefinition::class, 'id'),
            new ManyToOneAssociationField('tags', 'tag_id', TagDefinition::class, 'id')
        ]);
    }
}
