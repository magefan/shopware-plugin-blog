/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const { Module } = Shopware;

/* Pages */
import './page/blog-post-list';
import './page/blog-post-detail';
import './page/blog-post-create';

/* Components */
import './component/blog-post-detail-base';
import './component/blog-post-display-setting';
import './component/blog-post-seo';
import './component/blog-post-clone-modal';
import './component/blog-post-media-form'
import './component/blog-post-short-content'

/* Translations  */
import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

/* Acl */
import './acl'

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
                parentPath: 'sw-content',
                privilege: 'blog_post.viewer',
            }
        },
        create: {
            component: 'blog-post-create',
            path: 'create',
            meta: {
                privilege: 'blog_post.creator',
                parentPath: 'blog.post.index',
            },
        },
        detail: {
            component: 'blog-post-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'blog.post.index',
                privilege: 'blog_post.viewer',
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
        privilege: 'blog_post.viewer',
        parent: 'sw-content',
    }],
});
