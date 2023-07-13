<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\DataResolver;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class BlogAbstractResolver
{
    /**
     * @var SystemConfigService
     */
    protected SystemConfigService $systemConfigService;

    /**
     * @var EntityRepository
     */
    private EntityRepository $blogAuthorRepository;

    /**
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @param Request $request
     * @return int
     */
    public function getPage(Request $request): int
    {
        $page = $request->query->getInt('page', 1);

        if ($request->isMethod(Request::METHOD_POST)) {
            $page = $request->request->getInt('page', $page);
        }

        return $page <= 0 ? 1 : $page;
    }

    /**
     * @param $items
     * @return array
     */
    public function getPagination($items): array
    {
        $limit = (int)$this->systemConfigService->get('MagefanBlog.config.postsPerPagePostList');
        $totalPosts = count($items);
        if (!$limit) {
            $limit = 10;
        }

        $pages = [];
        $pagesCount = $totalPosts / $limit;
        for ($i = 1; $i <= ceil($pagesCount); $i++) {
            $pages[$i] = $totalPosts / $limit > 1 ? $limit : $totalPosts;
            $totalPosts = $totalPosts - $limit;
        }
        return $pages;
    }
}
