<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663319539CreateBlogPostTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663319539;
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            '
                 CREATE TABLE `magefanblog_post` (
                 `id` binary(16) NOT NULL COMMENT "Post ID",
                 `title` varchar(255) NOT NULL COMMENT "Post Title",
                 `meta_title` varchar(255) DEFAULT NULL COMMENT "Post Meta Title",
                 `meta_keywords` text COMMENT "Post Meta Keywords",
                 `meta_description` text COMMENT "Post Meta Description",
                 `identifier` varchar(100) DEFAULT NULL COMMENT "Post String Identifier",
                 `og_title` varchar(255) DEFAULT NULL COMMENT "Post OG Title",
                 `og_description` varchar(255) DEFAULT NULL COMMENT "Post OG Description",
                 `og_img` binary(16) DEFAULT NULL COMMENT "Post OG Img",
                 `og_type` varchar(255) DEFAULT NULL COMMENT "Post OG Type",
                 `content_heading` mediumtext DEFAULT NULL COMMENT "Post Content Heading",
                 `content` mediumtext COMMENT "Post Content",
                 `publish_time` DATETIME(3) NULL DEFAULT NULL COMMENT "Post Publish Time",
                 `is_active` smallint NOT NULL DEFAULT 1 COMMENT "Is Post Active",
                 `position` smallint NOT NULL DEFAULT 0 COMMENT "Position",
                 `featured_img` varchar(255) DEFAULT NULL COMMENT "Thumbnail Image",
                 `featured_img_alt` varchar(255) DEFAULT NULL COMMENT "Featured Image Alt",
                 `author_id` binary(16) DEFAULT NULL COMMENT "Author ID",
                 `page_layout` varchar(255) DEFAULT NULL COMMENT "Post Layout",
                 `layout_update_xml` text COMMENT "Post Layout Update Content",
                 `custom_theme` varchar(100) DEFAULT NULL COMMENT "Post Custom Theme",
                 `custom_layout` varchar(255) DEFAULT NULL COMMENT "Post Custom Template",
                 `custom_layout_update_xml` text COMMENT "Post Custom Layout Update Content",
                 `custom_theme_from` date DEFAULT NULL COMMENT "Post Custom Theme Active From Date",
                 `custom_theme_to` date DEFAULT NULL COMMENT "Post Custom Theme Active To Date",
                 `media_gallery` mediumtext COMMENT "Media Gallery",
                 `include_in_recent` int DEFAULT NULL COMMENT "Include In Recent Post",
                 `secret` varchar(32) DEFAULT NULL COMMENT "Post Secret",
                 `views_count` int DEFAULT NULL COMMENT "Post Views Count",
                 `is_recent_posts_skip` smallint DEFAULT NULL COMMENT "Is Post Skipped From Recent Posts",
                 `short_content` mediumtext COMMENT "Post Short Content",
                 `comments_count` int DEFAULT NULL COMMENT "Post Comment Counts",
                 `media_id` binary(16) DEFAULT NULL COMMENT "Media Id",
                 `post_media_version_id` binary(16) DEFAULT NULL,
                 `created_at` DATETIME(3) NOT NULL COMMENT "Post Comment Counts",
                 `updated_at` DATETIME(3) NULL COMMENT "Post Comment Counts", 
                 PRIMARY KEY (`id`),
                 KEY `MAGEFANBLOG_POST_IDENTIFIER` (`identifier`),
                 KEY `MAGEFANBLOG_POST_AUTHOR_ID` (`author_id`),
                 KEY `MAGEFANBLOG_POST_VIEWS_COUNT` (`views_count`),
                 KEY `MAGEFANBLOG_POST_IS_RECENT_POSTS_SKIP` (`is_recent_posts_skip`),
                 FULLTEXT KEY `FTI_A31A6CE1BAE9596AD2A53A8D37C22351` (`title`,`meta_keywords`,`meta_description`,`identifier`,`content`)
                ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Post Table";
            '
        );

        $connection->executeStatement(
            '
             INSERT INTO `magefanblog_post` (
              `id`, `title`, `meta_title`, 
              `meta_keywords`, `meta_description`, 
              `identifier`, `og_title`, `og_description`, 
              `og_img`, `og_type`, `content_heading`, 
              `content`, `created_at`, `updated_at`, 
              `publish_time`, `is_active`, `include_in_recent`, 
              `position`, `featured_img`, `featured_img_alt`, 
              `author_id`, `page_layout`, `layout_update_xml`, 
              `custom_theme`, `custom_layout`, 
              `custom_layout_update_xml`, `custom_theme_from`, 
              `custom_theme_to`, `media_gallery`, 
              `secret`, `views_count`, `is_recent_posts_skip`, 
              `short_content`, `comments_count`
            ) 
            VALUES 
              (
                :id, :title, 
                NULL, :metaKeywords, :metaDescription, 
                :identifier , NULL, 
                NULL, NULL, NULL, :contentHeading, 
                :content,:createdAt, :updatedAt, 
               :publishTime, 1, 1, 0, NULL, 
                NULL, NULL, NULL, NULL, NULL, NULL, 
                NULL, NULL, NULL, NULL, NULL, NULL, 
                NULL, NULL, 0
              );
        ',
            [
                        'id' => Uuid::randomBytes(),
                        'title' => 'Magefan Blog Post Sample',
                        'identifier' => 'magefan-blog-post-sample',
                        'metaKeywords' => 'Magefan blog sample',
                        'metaDescription' => 'Magefan blog default post.',
                        'contentHeading' => 'Magefan Blog Post Sample',
                        'content' => '<p>Welcome to Blog extension by Magefan.\n This is your first post. Edit or delete it, then start blogging!\n </p>',
                        'publishTime' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                        'updatedAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                        'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
                ]
        );
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
