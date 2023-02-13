/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-detail-base.html.twig';

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

Component.register('blog-category-detail-base', {
    template,

    inject: ['acl'],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        category: {
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
            categories: [
                { value: 'media', label: this.$tc('blog-category.detail.mediaDisplayType') },
                { value: 'text', label: this.$tc('blog-category.detail.textDisplayType') },
                { value: 'select', label: this.$tc('blog-category.detail.selectDisplayType') },
                { value: 'color', label: this.$tc('blog-category.detail.colorDisplayType') },
            ],
        };
    },

    computed: {
        ...mapPropertyErrors('category', ['title']),
    },

    created() {
        if (!this.category.title){
            this.category.isActive = true;
        }
    }
});
