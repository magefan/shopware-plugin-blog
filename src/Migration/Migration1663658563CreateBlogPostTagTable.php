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

class Migration1663658563CreateBlogPostTagTable extends MigrationStep
{
    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1663658563;
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
                CREATE TABLE `magefanblog_post_tag` (
                 `id` binary(16) NOT NULL COMMENT "Depend ID",
                 `post_id` binary(16) NOT NULL COMMENT "Post ID",
                 `tag_id` binary(16) NOT NULL COMMENT "Tag ID",
                 `created_at` DATETIME(3) NOT NULL COMMENT "Depend Created At",
                 `updated_at` DATETIME(3) NULL COMMENT "Depend Updated At",
                 PRIMARY KEY (`id`),
                 KEY `MAGEFANBLOG_POST_TAG_TAG_ID` (`tag_id`)
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
