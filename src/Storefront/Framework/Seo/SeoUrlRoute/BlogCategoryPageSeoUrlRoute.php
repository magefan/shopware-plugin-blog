<?php declare(strict_types=1);

namespace Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute;

use Magefan\Blog\Core\Content\Blog\Category\CategoryDefinition;
use Magefan\Blog\Core\Content\Blog\Category\CategoryEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlogCategoryPageSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.blog.category';

    public const  DEFAULT_TEMPLATE = 'blog/category/{{ category.identifier }}';

    /**
     * @var CategoryDefinition
     */
    private $definition;

    /**
     * @param CategoryDefinition $definition
     */
    public function __construct(CategoryDefinition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return SeoUrlRouteConfig
     */
    public function getConfig(): SeoUrlRouteConfig
    {
        return new SeoUrlRouteConfig(
            $this->definition,
            self::ROUTE_NAME,
            self::DEFAULT_TEMPLATE
        );
    }

    /**
     * @param Criteria $criteria
     * @return void
     */
    public function prepareCriteria(Criteria $criteria): void
    {
/*        $criteria->addAssociations([
            'blogCategories',
            'blogAuthor',
        ]);*/
    }

    /**
     * @param Entity $entry
     * @param SalesChannelEntity|null $salesChannel
     * @return SeoUrlMapping
     */
    public function getMapping(Entity $entry, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        if (!$entry instanceof CategoryEntity) {
            throw new \InvalidArgumentException('Expected CategoryEntity');
        }

        return new SeoUrlMapping(
            $entry,
            ['identifier' => $entry->getId()],
            [
                'category' => $entry,
            ]
        );
    }
}
