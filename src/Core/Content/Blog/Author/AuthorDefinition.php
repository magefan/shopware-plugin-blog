<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Author;

use Magefan\Blog\Core\Content\Blog\Author\AuthorTranslation\AuthorTranslationDefinition;
use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;

class AuthorDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'magefanblog_author';

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
        return AuthorEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return AuthorCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new IdField('admin_user_id', 'adminUserId'))->addFlags(new Required()),
            new FkField('media_id', 'mediaId', MediaDefinition::class),

            (new StringField('firstname', 'firstname')),
            (new StringField('lastname', 'lastname')),
            (new StringField('facebook_page_url', 'facebookPageUrl')),
            (new StringField('twitter_page_url', 'twitterPageUrl')),
            (new StringField('instagram_page_url', 'instagramPageUrl')),
            (new StringField('googleplus_page_url', 'googleplusPageUrl')),
            (new StringField('linkedin_page_url', 'linkedinPageUrl')),
            (new StringField('meta_title', 'metaTitle')),
            (new StringField('meta_keywords', 'metaKeywords')),
            (new StringField('meta_description', 'metaDescription')),
            (new StringField('meta_robots', 'metaRobots')),
            (new StringField('identifier', 'identifier')),
            (new LongTextField('content', 'content')),
            (new LongTextField('short_content', 'short_content')),
            (new StringField('featured_img', 'featuredImg')),
            (new BoolField('is_active', 'isActive')),
            (new StringField('email', 'email')),
            (new StringField('role', 'role')),
            (new StringField('page_layout', 'pageLayout')),
            (new StringField('layout_update_xml', 'layoutUpdateXml')),
            (new StringField('custom_theme', 'customTheme')),
            (new StringField('custom_layout', 'customLayout')),
            (new StringField('custom_layout_update_xml', 'customLayoutUpdateXml')),
            (new DateField('custom_theme_from', 'customThemeFrom')),
            (new DateField('custom_theme_to', 'customThemeTo')),
            (new IntField('posts_per_page', 'postsPerPage')),
            (new StringField('posts_list_template', 'postsListTemplate')),
            (new IdField('media_id', 'mediaId')),
            (new StringField('created_at', 'createdAt')),
            (new StringField('updated_at', 'updatedAt')),

            // associations
            new OneToManyAssociationField('authorPosts', PostDefinition::class, 'author_id'),
            (new OneToOneAssociationField('media', 'media_id', 'id', MediaDefinition::class, true))->addFlags(new ApiAware()),
        ]);
    }
}
