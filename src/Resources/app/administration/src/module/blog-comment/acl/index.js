/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'catalogues',
        key: 'comment',
        roles: {
            viewer: {
                privileges: [
                    'blog_comment:create',
                    'blog_comment:update',
                    'blog_comment:read',
                    Shopware.Service('privileges').getPrivileges('cms.viewer'),
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'blog_comment:create',
                    'blog_comment:delete',
                ],
                dependencies: [
                    'blog_comment.viewer',
                ],
            },
            creator: {
                privileges: [
                    'blog_comment:create',
                ],
                dependencies: [
                    'blog_comment.viewer',
                    'blog_comment.editor',
                ],
            },
            deleter: {
                privileges: [
                    'blog_comment:delete',
                ],
                dependencies: [
                    'blog_comment.viewer',
                ],
            },
        },
    });
