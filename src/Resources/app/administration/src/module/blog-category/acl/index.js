/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'permissions',
        parent: 'catalogues',
        key: 'category',
        roles: {
            viewer: {
                privileges: [
                    'blog_category:create',
                    'blog_category:update',
                    'blog_category:read',
                    Shopware.Service('privileges').getPrivileges('cms.viewer'),
                ],
                dependencies: [],
            },
            editor: {
                privileges: [
                    'blog_category:create',
                    'blog_category:delete',
                ],
                dependencies: [
                    'blog_category.viewer',
                ],
            },
            creator: {
                privileges: [
                    'blog_category:create',
                ],
                dependencies: [
                    'blog_category.viewer',
                    'blog_category.editor',
                ],
            },
            deleter: {
                privileges: [
                    'blog_category:delete',
                ],
                dependencies: [
                    'blog_category.viewer',
                ],
            },
        },
    });
