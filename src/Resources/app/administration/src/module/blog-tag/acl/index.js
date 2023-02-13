/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'catalogues',
        key: 'tag',
        roles: {
            viewer: {
                privileges: [
                    'blog_tag:create',
                    'blog_tag:update',
                    'blog_tag:read',
                    Shopware.Service('privileges').getPrivileges('cms.viewer'),
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'blog_tag:create',
                    'blog_tag:delete',
                ],
                dependencies: [
                    'blog_tag.viewer',
                ],
            },
            creator: {
                privileges: [
                    'blog_tag:create',
                ],
                dependencies: [
                    'blog_tag.viewer',
                    'blog_tag.editor',
                ],
            },
            deleter: {
                privileges: [
                    'blog_tag:delete',
                ],
                dependencies: [
                    'blog_tag.viewer',
                ],
            },
        },
    });
