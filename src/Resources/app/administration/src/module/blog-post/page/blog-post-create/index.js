/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-create.html.twig';

const { Component } = Shopware;

Component.extend('blog-post-create', 'blog-post-detail', {
    template,

    data() {
        return {
            newId: null,
        };
    },

    methods: {
        createdComponent() {

            this.post = this.postRepository.create();
            this.newId = this.post.id;

            this.isLoading = false;
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({ name: 'blog.post.detail', params: { id: this.newId } });
        },

        onSave() {
            this.$super('onSave');
        },
    },
});
