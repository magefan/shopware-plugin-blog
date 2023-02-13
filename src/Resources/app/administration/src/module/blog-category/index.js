/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const { Module } = Shopware;
import './page/blog-category-list';
import './page/blog-category-detail';
import './component/blog-category-design';
import './component/blog-category-detail-base';
import './component/blog-category-display-setting';
import './component/blog-category-seo';
import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

Module.register('blog-category', {
    type: 'plugin',
    title: 'blog-category.general.title',
    description: 'blog-post.general.descriptionTextModule',
    color: '#ff68b4',
    icon: 'regular-content',
    favicon: 'icon-module-content.png',
    entity: 'tags',
    snippets: {
        'en-GB': enGB,
        'de-DE': deDE
    },

    routes: {
        index: {
            component: 'blog-category-list',
            path: 'list',
            meta: {
                parentPath: 'sw-content'
            }
        },
        create: {
            component: 'blog-category-detail',
            path: 'create',
            meta: {
                parentPath: 'blog.category.index',
            },
        },
        detail: {
            component: 'blog-category-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'blog.category.index',
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
        id: 'blog.category.index',
        label: 'blog-category.general.mainMenuItemList',
        color: '#ff68b4',
        path: 'blog.category.index',
        icon: 'regular-content',
        position: 12,
        parent: 'sw-content',
    }],
});