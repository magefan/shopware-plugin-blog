/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'content',
        key: 'blog_tag',
        roles: {
            viewer: {
                privileges: [
                    'magefanblog_tag:create',
                    'magefanblog_tag:update',
                    'magefanblog_tag:read',
                    'magefanblog_post_tag:create',
                    'magefanblog_post_tag:update',
                    'magefanblog_post_tag:read',
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'magefanblog_tag:create',
                    'magefanblog_tag:delete',
                    'magefanblog_post_tag:create',
                    'magefanblog_post_tag:delete',
                ],
                dependencies: [
                    'blog_tag.viewer',
                ],
            },
            creator: {
                privileges: [
                    'magefanblog_tag:create',
                    'magefanblog_post_tag:create',
                ],
                dependencies: [
                    'blog_tag.viewer',
                    'blog_tag.editor',
                ],
            },
            deleter: {
                privileges: [
                    'magefanblog_tag:delete',
                    'magefanblog_post_tag:delete',
                ],
                dependencies: [
                    'blog_tag.viewer',
                ],
            },
        },
    });
