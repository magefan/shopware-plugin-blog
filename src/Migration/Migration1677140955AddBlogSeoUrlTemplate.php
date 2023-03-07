<?php declare(strict_types=1);

namespace Magefan\Blog\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1677140955AddBlogSeoUrlTemplate extends MigrationStep
{
    const TEMPLATES_DETAIL = [
        'Post' => 'magefanblog_post',
        'Category' => 'magefanblog_category',
        'Tag' => 'magefanblog_tag'
    ];

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1677140955;
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        foreach (self::TEMPLATES_DETAIL as $name => $entityDetail) {
            $className = '\Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute\Blog' . $name . 'PageSeoUrlRoute';
            if ($entityDetail) {
                $connection->insert('seo_url_template', [
                    'id' => Uuid::randomBytes(),
                    'sales_channel_id' => null,
                    'route_name' => $className::ROUTE_NAME,
                    'entity_name' => $entityDetail,
                    'template' => $className::DEFAULT_TEMPLATE,
                    'created_at' => (new \DateTimeImmutable())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }
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
