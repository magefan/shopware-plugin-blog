/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'content',
        key: 'blog_category',
        roles: {
            viewer: {
                privileges: [
                    'magefanblog_category:create',
                    'magefanblog_category:update',
                    'magefanblog_category:read',
                    'magefanblog_post_category:create',
                    'magefanblog_post_category:update',
                    'magefanblog_post_category:read',
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'magefanblog_category:create',
                    'magefanblog_category:delete',
                    'magefanblog_author:create',
                    'magefanblog_author:delete',
                ],
                dependencies: [
                    'blog_category.viewer',
                ],
            },
            creator: {
                privileges: [
                    'magefanblog_category:create',
                    'magefanblog_author:create',
                ],
                dependencies: [
                    'blog_category.viewer',
                    'blog_category.editor',
                ],
            },
            deleter: {
                privileges: [
                    'magefanblog_category:delete',
                    'magefanblog_author:delete',
                ],
                dependencies: [
                    'blog_category.viewer',
                ],
            },
        },
    });
