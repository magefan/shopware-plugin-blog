<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post;

use Magefan\Blog\Core\Content\Blog\Author\AuthorDefinition;
use Magefan\Blog\Core\Content\Blog\Category\CategoryDefinition;
use Magefan\Blog\Core\Content\Blog\Comment\CommentDefinition;
use Magefan\Blog\Core\Content\Blog\Post\PostTranslation\PostTranslationDefinition;
use Magefan\Blog\Core\Content\Blog\PostCategory\PostCategoryDefinition;
use Magefan\Blog\Core\Content\Blog\PostTag\PostTagDefinition;
use Magefan\Blog\Core\Content\Blog\Tag\TagDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;

class PostDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_post';

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
        return PostEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return PostCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new FkField('author_id', 'authorId', AuthorDefinition::class),

            //translations
            (new TranslatedField('title'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaTitle'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaKeywords'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('metaDescription'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('contentHeading'))->addFlags(new ApiAware(), new AllowHtml()),
            (new TranslatedField('content'))->addFlags(new ApiAware(), new AllowHtml()),
            (new TranslatedField('featuredImg'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('featuredImgAlt'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('ogTitle'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('ogDescription'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('ogImg'))->addFlags(new ApiAware(), new Inherited()),
            (new TranslatedField('ogType'))->addFlags(new ApiAware(), new Inherited()),

            (new StringField('identifier', 'identifier'))->addFlags(new Required()),
            (new IntField('position', 'position')),
            (new BoolField('include_in_recent', 'includeInRecent')),
            (new DateTimeField('publish_time', 'publishTime')),
            (new StringField('created_at', 'createdAt')),
            (new StringField('updated_at', 'updatedAt')),
            (new DateTimeField('publish_time', 'publishTime')),
            (new BoolField('is_active', 'isActive')),

            // associations
            (new OneToOneAssociationField('media', 'media_id', 'id', MediaDefinition::class, true))->addFlags(new ApiAware()),
            new FkField('author_id', 'authorId', AuthorDefinition::class),
            new ManyToOneAssociationField('postAuthor', 'author_id', AuthorDefinition::class, 'id'),
            new ManyToManyAssociationField('postTags', TagDefinition::class, PostTagDefinition::class, 'post_id', 'tag_id'),
            new ManyToManyAssociationField('postCategories', CategoryDefinition::class, PostCategoryDefinition::class, 'post_id', 'category_id'),
            new OneToManyAssociationField('postComments', CommentDefinition::class, 'post_id'),
            (new TranslationsAssociationField(PostTranslationDefinition::class, 'magefanblog_post_id'))->addFlags(new ApiAware(), new Required())
        ]);
    }
}
