/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-short-content.html.twig';

const {Component} = Shopware;

Component.register('blog-post-short-content', {
    template,

    inject: ['acl'],

    props: {
        post: {
            type: Object,
            required: true,
            default() {
                return {};
            },
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
    },

});
