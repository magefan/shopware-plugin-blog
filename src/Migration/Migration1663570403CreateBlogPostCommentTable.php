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

class Migration1663570403CreateBlogPostCommentTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663570403;
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
                CREATE TABLE `magefanblog_comment` (
                 `id` binary(16) NOT NULL  COMMENT "Comment ID",
                 `parent_id` binary(16) DEFAULT NULL COMMENT "Parent Comment ID",
                 `post_id` binary(16) NOT NULL COMMENT "Post ID",
                 `customer_id` binary(16) DEFAULT NULL COMMENT "Customer ID",
                 `admin_id` binary(16) DEFAULT NULL COMMENT "Admin User ID",
                 `status` smallint NOT NULL DEFAULT 1 COMMENT "Comment status",
                 `author_type` smallint NOT NULL COMMENT "Author Type",
                 `author_nickname` varchar(255) NOT NULL COMMENT "Comment Author Nickname",
                 `author_email` varchar(255) DEFAULT NULL COMMENT "Comment Author Email",
                 `text` mediumtext COMMENT "Text",
                 `created_at` DATETIME(3) NOT NULL COMMENT "Comment Creation Time",
                 `updated_at` DATETIME(3) NULL COMMENT "Comment Update Time",
                 PRIMARY KEY (`id`),
                 KEY `MAGEFAN_BLOG_COMMENT_PARENT_ID` (`parent_id`),
                 KEY `MAGEFAN_BLOG_COMMENT_POST_ID` (`id`),
                 KEY `MAGEFAN_BLOG_COMMENT_CUSTOMER_ID` (`customer_id`),
                 KEY `MAGEFAN_BLOG_COMMENT_ADMIN_ID` (`admin_id`),
                 KEY `MAGEFAN_BLOG_COMMENT_STATUS` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT="magefanblog_comment"
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
