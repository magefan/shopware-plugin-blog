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

class Migration1663321745CreateBlogCategoryTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663321745;
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
                CREATE TABLE `magefanblog_category` (
                 `id` binary(16) NOT NULL COMMENT "Category ID",
                 `position` smallint DEFAULT 10 COMMENT "Category Position",
                 `identifier` varchar(100) DEFAULT NULL COMMENT "Category String Identifier",
                 `posts_sort_by` varchar(15) DEFAULT "createdAt" NOT NULL COMMENT "Category Sort By",
                 `include_in_menu` smallint DEFAULT NULL COMMENT "Category In Menu",
                 `is_active` smallint NOT NULL DEFAULT 1 COMMENT "Is Category Active",
                 `display_mode` smallint NOT NULL DEFAULT 0 COMMENT "Display Mode",
                 `page_layout` varchar(255) DEFAULT NULL COMMENT "Category Layout",
                 `layout_update_xml` text COMMENT "Category Layout Update Content",
                 `custom_theme` varchar(100) DEFAULT NULL COMMENT "Category Custom Theme",
                 `custom_layout` varchar(255) DEFAULT NULL COMMENT "Category Custom Template",
                 `custom_layout_update_xml` text COMMENT "Category Custom Layout Update Content",
                 `custom_theme_from` date DEFAULT NULL COMMENT "Category Custom Theme Active From Date",
                 `custom_theme_to` date DEFAULT NULL COMMENT "Category Custom Theme Active To Date",
                 `posts_per_page` int DEFAULT NULL COMMENT "Posts Per Page",
                 `posts_list_template` varchar(100) DEFAULT NULL COMMENT "Posts List Template",
                 `created_at` DATETIME(3) NOT NULL,
                 `updated_at` DATETIME(3) NULL,
                 PRIMARY KEY (`id`),
                 KEY `MAGEFANBLOG_POST_IDENTIFIER` (`identifier`),
                 KEY `MYM2MAGEFAN_BLOG_CATEGORY_INCLUDE_IN_MENU` (`include_in_menu`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Category Table"
            '
        );

        $connection->executeStatement(
            '
                 CREATE TABLE `magefanblog_category_translation` (
                 `magefanblog_category_id` binary(16) NOT NULL COMMENT "Category ID",
                 `language_id` BINARY(16) NOT NULL COMMENT "Language Id",
                 `title` varchar(255) DEFAULT NULL COMMENT "Category Title",
                 `meta_title` varchar(255) DEFAULT NULL COMMENT "Category Meta Title",
                 `meta_keywords` text COMMENT "Category Meta Keywords",
                 `meta_description` text COMMENT "Category Meta Description",
                 `content_heading` mediumtext DEFAULT NULL COMMENT "Category Content Heading",
                 `content` mediumtext COMMENT "Category Content",
                 `path` varchar(255) DEFAULT NULL COMMENT "Category Path",
                 `created_at` DATETIME(3) NOT NULL COMMENT "Category Translation Created At",
                 `updated_at` DATETIME(3) NULL COMMENT "Category Translation Updated At", 
                 PRIMARY KEY (`magefanblog_category_id`, `language_id`),
                 FULLTEXT KEY `FTI_A31A6CE1BAE9596AD2A53A8D37C22351` (`title`,`meta_keywords`,`meta_description`,`content`)
                ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Category Table Translation";
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
