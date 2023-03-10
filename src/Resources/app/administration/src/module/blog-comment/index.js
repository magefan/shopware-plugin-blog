/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const { Module } = Shopware;

/* Pages */
import './page/blog-comment-list';
import './page/blog-comment-detail';

/* Translations */
import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

/* Acl */
import './acl';

import mfDefaultSearchConfiguration from './mf-default-search-configuration';

Module.register('blog-comment', {
    type: 'plugin',
    title: 'blog-comment.general.title',
    description: 'blog-comment.general.descriptionTextModule',
    color: '#ff68b4',
    icon: 'regular-content',
    favicon: 'icon-module-content.png',
    entity: 'magefanblog_comment',
    snippets: {
        'en-GB': enGB,
        'de-DE': deDE
    },

    routes: {
        index: {
            components: {
                default: 'blog-comment-list',
            },
            path: 'list',
            meta: {
                privilege: 'blog_comment.viewer',
                appSystem: {
                    view: 'list',
                },
            },
        },

        detail: {
            component: 'blog-comment-detail',
            path: 'detail/:id?',
            props: {
                default: (route) => ({ id: route.params.id }),
            },

            meta: {
                privilege: 'blog_comment.viewer',
                parentPath: 'blog.comment.index',
                appSystem: {
                    view: 'detail',
                },
            },
        },
    },

    navigation: [{
        id: 'blog.comment.index',
        label: 'blog-comment.general.mainMenuItemList',
        color: '#ff68b4',
        path: 'blog.comment.index',
        icon: 'regular-content',
        position: 15,
        parent: 'sw-content',
        privilege: 'blog_comment.viewer',
    }],

    mfDefaultSearchConfiguration,
});
