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

class Migration1663321276CreateBlogPostToCategoryLinkTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1963321276;
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
                CREATE TABLE `magefanblog_post_category` (
                 `id` binary(16) NOT NULL COMMENT "Depend ID",
                 `post_id` binary(16) NOT NULL COMMENT "Post ID",
                 `category_id` binary(16) NOT NULL COMMENT "Category ID",
                 PRIMARY KEY (`id`, `post_id`,`category_id`),
                 `created_at` DATETIME(3) NOT NULL,
                 `updated_at` DATETIME(3) NULL,
                 KEY `MAGEFAN_BLOG_POST_CATEGORY_CATEGORY_ID` (`category_id`),
                 CONSTRAINT `MAGEFAN_BLOG_POST_CATEGORY_POST_ID_MAGEFAN_BLOG_POST_POST_ID` FOREIGN KEY (`post_id`) REFERENCES `magefanblog_post` (`id`) ON DELETE CASCADE,
                 CONSTRAINT `MAGEFAN_BLOG_POST_CTGR_CTGR_ID_MAGEFAN_BLOG_CTGR_CTGR_ID` FOREIGN KEY (`category_id`) REFERENCES `magefanblog_category` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT="Magefan Blog Post To Category Linkage Table"
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
