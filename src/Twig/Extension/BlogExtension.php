<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\Blog\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class BlogExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('get_child', [BlogRuntime::class, 'getChild']),
            new TwigFilter('getPostComments', [BlogRuntime::class, 'getPostComments']),
            new TwigFilter('getBreadcrumbsData', [BlogRuntime::class, 'getBreadcrumbsData']),
        ];
    }
}
