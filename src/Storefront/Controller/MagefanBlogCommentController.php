<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Storefront\Controller;

use Magefan\Blog\Core\Content\Blog\Post\PostEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MagefanBlogCommentController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private EntityRepository $postRepository;

    /**
     * @var EntityRepository
     */
    private EntityRepository $commentRepository;

    /**
     * @var SystemConfigService
     */
    private SystemConfigService $systemConfigService;

    /**
     * MagefanBlogCommentController constructor.
     * @param EntityRepository $postRepository
     * @param EntityRepository $commentRepository
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepository $postRepository,
        EntityRepository $commentRepository,
        SystemConfigService $systemConfigService
    ) {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route("/addcomment", name="frontend.blog.addcomment", defaults={"XmlHttpRequest"=true}, methods={"POST"})
     * @param RequestDataBag $data
     * @param SalesChannelContext $salesChannelContext
     * @param Context $context
     * @return JsonResponse
     */
    public function addComment(RequestDataBag $data, SalesChannelContext $salesChannelContext, Context $context): JsonResponse
    {
        $comment = [];
        foreach ($data->all() as $key => $value) {
            if ($key != '_csrf_token') {
                $key = str_replace(
                    '_',
                    '',
                    lcfirst(ucwords($key, '_'))
                );
            }
            $comment[$key] = $value;
        }

        $customer = $salesChannelContext->getCustomer();
        if ($customer) {
            $comment['customerId'] = $customer->getId();
            $comment['authorNickname'] = $customer->__toString();
            $comment['authorEmail'] = $customer->getEmail();
            $comment['authorType'] = 1;
        } elseif ($this->systemConfigService->get('MagefanBlog.config.guestComments') == 1) {
            if (!trim($data->get('author_nickname')) || !trim($data->get('author_email'))) {
                $response = [
                    'type' => 'danger',
                    'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                        'type' => 'danger',
                        'content' => $this->trans('Please enter your name and email.'),
                    ]),
                ];

                return new JsonResponse($response);
            }

            $comment['authorType'] = 0;
        } else {
            $response = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'content' => $this->trans('Login to submit comment.'),
                ]),
            ];

            return new JsonResponse($response);
        }

        if (!$comment['parentId']){
            unset($comment['parentId']);
        }

        foreach (['commentId', 'creationTime', 'updateTime', 'status'] as $key) {
            unset($comment[$key]);
        }

        $comment['status'] = (int)$this->systemConfigService->get('MagefanBlog.config.status');

        try {
            $post = $this->initPost($context, (string)$data->get('post_id'));
            if (!$post) {
                throw new \Exception($this->trans('You cannot post comment. Blog post is not longer exist.'));
            }
        } catch (\Exception $e) {
            $response = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'content' => $this->trans($e->getMessage()),
                ]),
            ];

            return new JsonResponse($response);
        }

        $this->commentRepository->create([$comment], $context);

        $response = [
            'type' => 'success',
            'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                'type' => 'success',
                'content' => $this->systemConfigService->get('MagefanBlog.config.status') == 0 ?
                    $this->trans('You submitted your comment for moderation.') :
                    $this->trans('Thank you for your comment.')
            ]),
        ];

        return new JsonResponse($response);
    }

    /**
     * @param Context $context
     * @param string $postId
     * @return PostEntity|null
     */
    private function initPost(Context $context, string $postId): ?PostEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $postId));
        $post = $this->postRepository->search($criteria, $context)->first();
        if (!$post->get('isActive')) {
            return null;
        }
        return $post;
    }
}
