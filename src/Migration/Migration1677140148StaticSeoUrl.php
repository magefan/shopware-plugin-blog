<?php declare(strict_types=1);

namespace Magefan\Blog\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Migration\Traits\ImportTranslationsTrait;
use Shopware\Core\Migration\Traits\Translations;

class Migration1677140148StaticSeoUrl extends MigrationStep
{
    use ImportTranslationsTrait;

    /**
     * @return int
     */
    public function getCreationTimestamp(): int
    {
        return 1677140148;
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $id = '265e0b57d2c54b49ba9e9e36ad4bc981';
        $translationUrl = [];
        foreach ($this->getLanguages($connection) as $language) {

            $translationUrl[] =
                [
                    'id' => Uuid::randomBytes(),
                    'sales_channel_id' => $this->getSalesChannelId($connection),
                    'foreign_key' => Uuid::fromHexToBytes($id),
                    'route_name' => 'frontend.blog.post',
                    'path_info' => '/blog/post/265e0b57d2c54b49ba9e9e36ad4bc981',
                    'is_canonical' => 1,
                    'is_modified' => 0,
                    'is_deleted' => 0,
                    'seo_path_info' => 'blog/post/magefan-blog-post-sample'
                ];
        }
        $this->importTranslation('seo_url', new Translations(
            $translationUrl[1] ?? $translationUrl[0],
                $translationUrl[0]
        ),
        $connection);
    }

    /**
     * @param Connection $connection
     * @return void
     */
    public function updateDestructive(Connection $connection): void
    {
    }

    /**
     * @param $connection
     * @return mixed
     */
    private function getLanguages($connection)
    {
        return $connection->executeQuery('SELECT DISTINCT `language_id` FROM `sales_channel_language`')->fetchAll();
    }

    /**
     * @param Connection $connection
     * @return string|null
     * @throws Exception
     */
    private function getSalesChannelId(Connection $connection): ?string
    {
        $sql = <<<SQL
            SELECT id
            FROM sales_channel
            WHERE type_id = :typeId
        SQL;
        $salesChannelId = $connection->fetchOne($sql, [
            ':typeId' => Uuid::fromHexToBytes(Defaults::SALES_CHANNEL_TYPE_STOREFRONT)
        ]);

        if (!$salesChannelId) {
            return null;
        }

        return $salesChannelId;
    }
}
