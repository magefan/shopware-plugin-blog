/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'content',
        key: 'blog_comment',
        roles: {
            viewer: {
                privileges: [
                    'magefanblog_comment:create',
                    'magefanblog_comment:update',
                    'magefanblog_comment:read',
                    'user_config:read'
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'magefanblog_comment:create',
                    'magefanblog_comment:delete',
                ],
                dependencies: [
                    'blog_comment.viewer',
                ],
            },
            creator: {
                privileges: [
                    'magefanblog_comment:create',
                ],
                dependencies: [
                    'blog_comment.viewer',
                    'blog_comment.editor',
                ],
            },
            deleter: {
                privileges: [
                    'magefanblog_comment:delete',
                ],
                dependencies: [
                    'blog_comment.viewer',
                ],
            },
        },
    });
