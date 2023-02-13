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

class Migration1663655011CreateBlogTagTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663655011;
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
                CREATE TABLE `magefanblog_tag` (
                 `id` binary(16) NOT NULL COMMENT "Tag ID",
                 `title` varchar(255) NOT NULL COMMENT "Tag Title",
                 `meta_robots` varchar(255) DEFAULT NULL COMMENT "Tag Default Robots",
                 `meta_description` varchar(255) DEFAULT NULL COMMENT "Tag Meta Description",
                 `meta_keywords` varchar(255) DEFAULT NULL COMMENT "Tag Meta Keywords",
                 `meta_title` varchar(255) DEFAULT NULL COMMENT "Tag Meta Title",
                 `identifier` varchar(100) DEFAULT NULL COMMENT "Tag String Identifier",
                 `page_layout` varchar(255) DEFAULT NULL COMMENT "Tag Layout",
                 `is_active` smallint NOT NULL DEFAULT 1 COMMENT "Is Tag Active",
                 `content` mediumtext COMMENT "Tag Content",
                 `layout_update_xml` text COMMENT "Tag Layout Update Content",
                 `custom_theme` varchar(100) DEFAULT NULL COMMENT "Tag Custom Theme",
                 `custom_layout` varchar(255) DEFAULT NULL COMMENT "Tag Custom Template",
                 `custom_layout_update_xml` text COMMENT "Tag Custom Layout Update Content",
                 `custom_theme_from` date DEFAULT NULL COMMENT "Tag Custom Theme Active From Date",
                 `custom_theme_to` date DEFAULT NULL COMMENT "Tag Custom Theme Active To Date",
                 `posts_per_page` int DEFAULT NULL COMMENT "Posts Per Page",
                 `posts_list_template` varchar(100) DEFAULT NULL COMMENT "Posts List Template",
                 `created_at` DATETIME(3) NOT NULL,
                 `updated_at` DATETIME(3) NULL,
                 PRIMARY KEY (`id`),
                 KEY `MAGEFANBLOG_TAG_IDENTIFIER` (`identifier`),
                 KEY `MAGEFANBLOG_TAG_active` (`is_active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Tag Table"
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
