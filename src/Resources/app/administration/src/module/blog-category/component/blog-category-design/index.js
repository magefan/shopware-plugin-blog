/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-design.html.twig';

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

Component.register('blog-category-design', {
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
            layoutTypes: [
                { value: 'empty', label: this.$tc('blog-category.detail.emptyLayout') },
                { value: 'column', label: this.$tc('blog-category.detail.oneColumnLayout') },
                { value: 'columnswithleft', label: this.$tc('blog-category.detail.columnsWithLeftLayout') },
                { value: 'columnswithright', label: this.$tc('blog-category.detail.columnsWithRightLayout') },
                { value: '3columns', label: this.$tc('blog-category.detail.3columnsLayout') },
            ],
        };
    },

    computed: {
        ...mapPropertyErrors('category', [
            'name',
            'displayType',
            'sortingType',
        ]),
    },
});
