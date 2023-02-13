/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'catalogues',
        key: 'post',
        roles: {
            viewer: {
                privileges: [
                    'blog_post:create',
                    'blog_post:update',
                    'blog_post:read',
                    Shopware.Service('privileges').getPrivileges('cms.viewer'),
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'blog_post:create',
                    'blog_post:delete',
                ],
                dependencies: [
                    'blog_post.viewer',
                ],
            },
            creator: {
                privileges: [
                    'blog_post:create',
                ],
                dependencies: [
                    'blog_post.viewer',
                    'blog_post.editor',
                ],
            },
            deleter: {
                privileges: [
                    'blog_post:delete',
                ],
                dependencies: [
                    'blog_post.viewer',
                ],
            },
        },
    });
