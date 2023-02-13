/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-display-setting.html.twig';

const {Component, Mixin} = Shopware;
const {mapPropertyErrors} = Component.getComponentHelper();

Component.register('blog-category-display-setting', {
    template,

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
            displayTypes: [
                {value: 'default', label: this.$tc('blog-category.detail.defaultTemplate')},
                /* { value: 'modern', label: this.$tc('blog-category.detail.modernTemplate') },*/
            ],
            sortByTypes: [
                {value: 'createdAt', label: this.$tc('blog-category.detail.defaultSort')},
                {value: 'position', label: this.$tc('blog-category.detail.positionSort')},
                {value: 'title', label: this.$tc('blog-category.detail.titleSort')},
            ]
        };
    },

    created() {
        if (!this.category.postsListTemplate) {
            this.category.postsListTemplate = 'default';
        }
        if (!this.category.postsSortBy) {
            this.category.postsSortBy = 'createdAt';
        }
    },
});
