<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Comment;

use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;

class CommentDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_comment';

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
        return CommentEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return CommentCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new IdField('parent_id', 'parentId')),
            (new IdField('customer_id', 'customerId')),
            (new IdField('admin_id', 'adminId')),
            (new IntField('status', 'status')),
            (new IntField('author_type', 'authorType')),
            (new StringField('author_nickname', 'authorNickname')),
            (new StringField('author_email', 'authorEmail')),
            (new StringField('text', 'text'))->addFlags(new Required()),
            (new DateField('created_at', 'createdAt')),
            (new DateField('updated_at', 'updatedAt')),
            (new FkField('post_id', 'postId', PostDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('post', 'post_id', PostDefinition::class, 'id'),
        ]);
    }
}
