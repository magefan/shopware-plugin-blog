/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'content',
        key: 'blog_post',
        roles: {
            viewer: {
                privileges: [
                    'magefanblog_post:create',
                    'magefanblog_post:update',
                    'magefanblog_post:read',
                    'magefanblog_author:create',
                    'magefanblog_author:update',
                    'magefanblog_author:read',
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'magefanblog_post:create',
                    'magefanblog_post:delete',
                    'magefanblog_author:create',
                    'magefanblog_author:delete',
                ],
                dependencies: [
                    'blog_post.viewer',
                ],
            },
            creator: {
                privileges: [
                    'magefanblog_post:create',
                    'magefanblog_author:create',
                ],
                dependencies: [
                    'blog_post.viewer',
                    'blog_post.editor',
                ],
            },
            deleter: {
                privileges: [
                    'magefanblog_post:delete',
                    'magefanblog_author:delete',
                ],
                dependencies: [
                    'blog_post.viewer',
                ],
            },
        },
    });
