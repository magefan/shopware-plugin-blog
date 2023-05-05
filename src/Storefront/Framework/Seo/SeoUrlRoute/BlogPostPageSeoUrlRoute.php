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

class BlogPostPageSeoUrlRoute implements SeoUrlRouteInterface
{
    public const ROUTE_NAME = 'frontend.blog.post';

    public const  DEFAULT_TEMPLATE = 'blog/post/{{ post.identifier }}';

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
        if (!$entry instanceof PostEntity) {
            throw new \InvalidArgumentException('Expected PostEntity');
        }

        return new SeoUrlMapping(
            $entry,
            ['identifier' => $entry->getId()],
            [
                'post' => $entry,
            ]
        );
    }
}
