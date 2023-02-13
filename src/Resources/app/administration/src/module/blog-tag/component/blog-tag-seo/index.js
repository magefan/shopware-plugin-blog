/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-tag-seo.html.twig';

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

Component.register('blog-tag-seo', {
    template,

    inject: ['acl'],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        tag: {
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
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    data() {
        return {
            metaRobots: [
                { value: 'config', label: this.$tc('blog-tag.detail.configRobots') },
                { value: 'INDEX, FOLLOW', label: this.$tc('blog-tag.detail.indexFollow') },
                { value: 'NOINDEX, FOLLOW', label: this.$tc('blog-tag.detail.noindexFollow') },
                { value: 'INDEX, NOFOLLOW', label: this.$tc('blog-tag.detail.indexNofollow') },
                { value: 'NOINDEX, NOFOLLOW', label: this.$tc('blog-tag.detail.noindexNofollow') },
            ],
        };
    },

    created() {
        if (!this.tag.metaRobots){
            this.tag.metaRobots = 'config';
        }
    },
});
