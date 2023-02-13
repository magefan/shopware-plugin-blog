/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-tag-create.html.twig';

const { Component } = Shopware;

Component.extend('blog-tag-create', 'blog-tag-detail', {
    template,

    data() {
        return {
            newId: null,
        };
    },

    methods: {
        createdComponent() {
            this.tag = this.tagRepository.create();
            this.newId = this.tag.id;

            this.isLoading = false;
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({ name: 'blog.tag.detail', params: { id: this.newId } });
        },

        onSave() {
            this.$super('onSave');
        },
    },
});
