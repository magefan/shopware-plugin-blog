<?php declare(strict_types=1);

namespace Magefan\Blog\Storefront\Framework\Seo\SeoUrlRoute;

use Magefan\Blog\Core\Content\Blog\Tag\TagDefinition;
use Magefan\Blog\Core\Content\Blog\Tag\TagEntity;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlMapping;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteConfig;
use Shopware\Core\Content\Seo\SeoUrlRoute\SeoUrlRouteInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class BlogTagPageSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.blog.tag';

    public const  DEFAULT_TEMPLATE = 'blog/tag/{{ tag.identifier }}';

    /**
     * @var TagDefinition
     */
    private $definition;

    /**
     * @param TagDefinition $definition
     */
    public function __construct(TagDefinition $definition)
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
        if (!$entry instanceof TagEntity) {
            throw new \InvalidArgumentException('Expected TagEntity');
        }

        return new SeoUrlMapping(
            $entry,
            ['identifier' => $entry->getId()],
            [
                'tag' => $entry,
            ]
        );
    }
}
