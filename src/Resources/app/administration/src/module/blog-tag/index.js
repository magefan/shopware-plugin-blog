/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

const { Module } = Shopware;

/* Pages */
import './page/blog-tag-list';
import './page/blog-tag-detail';
import './page/blog-tag-create';

/* Components */
import './component/blog-tag-detail-base';
import './component/blog-tag-display-setting';
import './component/blog-tag-seo';
import './component/blog-tag-clone-modal';

/* Translations */
import enGB from './snippet/en-GB';
import deDE from './snippet/de-DE';

/* Acl */
import './acl'

Module.register('blog-tag', {
    type: 'plugin',
    title: 'blog-tag.general.title',
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
            component: 'blog-tag-list',
            path: 'list',
            meta: {
                parentPath: 'sw-content',
                privilege: 'blog_tag.viewer',
            }
        },
        create: {
            component: 'blog-tag-create',
            path: 'create',
            meta: {
                parentPath: 'blog.tag.index',
                privilege: 'blog_tag.creator',
            },
        },
        detail: {
            component: 'blog-tag-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'blog.tag.index',
                privilege: 'blog_tag.viewer',
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
        id: 'blog.tag.index',
        label: 'blog-tag.general.mainMenuItemList',
        color: '#ff68b4',
        path: 'blog.tag.index',
        icon: 'regular-content',
        position: 13,
        privilege: 'blog_tag.viewer',
        parent: 'sw-content',
    }],
});
