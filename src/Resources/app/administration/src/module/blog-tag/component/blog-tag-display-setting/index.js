/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-tag-display-setting.html.twig';

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

Component.register('blog-tag-display-setting', {
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
            displayTypes: [
                { value: 'default', label: this.$tc('blog-tag.detail.defaultTemplate') },
              /*  { value: 'modern', label: this.$tc('blog-tag.detail.modernTemplate') },*/
            ],
        };
    },

    created() {
        if (!this.tag.postsListTemplate){
            this.tag.postsListTemplate = 'default';
        }
    },
});
