/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const { Module } = Shopware;
import './page/blog-post-list';
import './page/blog-post-detail';
import './page/blog-post-create';
import './component/blog-post-detail-base';
import './component/blog-post-display-setting';
import './component/blog-post-seo';
import './component/blog-post-clone-modal';
import './component/blog-post-media-form'
import './component/blog-post-short-content'
import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

Module.register('blog-post', {
    type: 'plugin',
    title: 'blog-post.general.title',
    description: 'blog-post.general.descriptionTextModule',
    color: '#ff68b4',
    icon: 'regular-content',
    favicon: 'icon-module-content.png',
    entity: 'post',
    snippets: {
        'en-GB': enGB,
        'de-DE': deDE
    },

    routes: {
        index: {
            component: 'blog-post-list',
            path: 'list',
            meta: {
                parentPath: 'sw-content'
            }
        },
        create: {
            component: 'blog-post-create',
            path: 'create',
            meta: {
                parentPath: 'blog.post.index',
            },
        },
        detail: {
            component: 'blog-post-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'blog.post.index',
            },
            props: {
                default(route) {
                    return {
                        id: route.params.id,
                    };
                },
            },
        },
    },

    navigation: [{
        id: 'blog.post.index',
        label: 'blog-post.general.mainMenuItemList',
        color: '#ff68b4',
        path: 'blog.post.index',
        icon: 'regular-content',
        position: 10,
        parent: 'sw-content',
    }],
});