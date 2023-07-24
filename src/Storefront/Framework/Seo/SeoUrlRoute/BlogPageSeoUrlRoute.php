<?php declare(strict_types=1);

namespace Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute;

use Magefan\Blog\Core\Content\Blog\Post\PostDefinition;
use Magefan\Blog\Core\Content\Blog\Post\PostEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlogPageSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.blog';

    public const  DEFAULT_TEMPLATE = 'blog';

    /**
     * @var PostDefinition
     */
    private $definition;

    /**
     * @param PostDefinition $definition
     */
    public function __construct(PostDefinition $definition)
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
     * @param Entity $entry
     * @param SalesChannelEntity|null $salesChannel
     * @return SeoUrlMapping
     */
    public function getMapping(Entity $entry, ?SalesChannelEntity $salesChannel): SeoUrlMapping
    {
        return new SeoUrlMapping($entry, [], []);
    }

    public function prepareCriteria(Criteria $criteria, SalesChannelEntity $salesChannel = null): void
    {
        // TODO: Implement prepareCriteria() method.
    }
}
