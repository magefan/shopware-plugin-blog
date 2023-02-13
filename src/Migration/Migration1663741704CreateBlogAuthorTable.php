<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663741704CreateBlogAuthorTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663741704;
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
                CREATE TABLE `magefanblog_author` (
                 `id` binary(16) NOT NULL COMMENT "Author ID",
                 `is_active` smallint NOT NULL DEFAULT 1 COMMENT "Is Author Active",
                 `firstname` varchar(255) DEFAULT NULL COMMENT "Author FirstName",
                 `lastname` varchar(255) DEFAULT NULL COMMENT "Author LastName",
                 `admin_user_id` binary(16) NOT NULL COMMENT "Admin user ID",
                 `email` varchar(255) DEFAULT NULL COMMENT "Author Email",
                 `role` varchar(255) DEFAULT NULL COMMENT "Author role (developer)",
                 `facebook_page_url` varchar(255) DEFAULT NULL COMMENT "Author in Facebook",
                 `twitter_page_url` varchar(255) DEFAULT NULL COMMENT "Author in Twitter",
                 `instagram_page_url` varchar(255) DEFAULT NULL COMMENT "Author in Instagram",
                 `googleplus_page_url` varchar(255) DEFAULT NULL COMMENT "Author in GooglePlus",
                 `linkedin_page_url` varchar(255) DEFAULT NULL COMMENT "Author in LinkedIn",
                 `meta_title` varchar(255) DEFAULT NULL COMMENT "Author Meta Title",
                 `meta_keywords` text COMMENT "Author Meta Keywords",
                 `meta_robots` varchar(20) DEFAULT "config" COMMENT "Author Meta Robots",
                 `meta_description` text COMMENT "Author Meta Description",
                 `identifier` varchar(100) DEFAULT NULL COMMENT "Author String Identifier",
                 `content` mediumtext COMMENT "Author Content",
                 `short_content` mediumtext COMMENT "Author Short Content",
                 `featured_img` varchar(255) DEFAULT NULL COMMENT "Author Image",
                 `page_layout` varchar(255) DEFAULT NULL COMMENT "Author Layout",
                 `layout_update_xml` text COMMENT "Author Layout Update Content",
                 `custom_theme` varchar(100) DEFAULT NULL COMMENT "Author Custom Thema",
                 `custom_layout` varchar(100) DEFAULT NULL COMMENT "Author Custom Template",
                 `custom_layout_update_xml` text COMMENT "Author Custom Layout Update Content",
                 `custom_theme_from` date DEFAULT NULL COMMENT "Author Custom Theme Active From Date",
                 `custom_theme_to` date DEFAULT NULL COMMENT "Author Custom Theme Active To Date",
                 `posts_per_page` int DEFAULT NULL COMMENT "Posts Per Page", 
                 `media_id` binary(16) DEFAULT NULL COMMENT "Image Id",
                 `posts_list_template` varchar(100) DEFAULT "default" COMMENT "Posts List Template",
                 `created_at` DATETIME(3) NOT NULL COMMENT "Comment Creation Time",
                 `updated_at` DATETIME(3) NULL COMMENT "Comment Update Time",
                 PRIMARY KEY (`id`),
                 KEY `MAGEFANBLOG_AUTHOR_IDENTIFIER` (`identifier`)
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Author Table"
            '
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
