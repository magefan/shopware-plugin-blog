/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-clone-modal.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('blog-post-clone-modal', {
    template,

    inject: ['repositoryFactory', 'numberRangeService'],

    props: {
        post: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            cloningVariants: false,
            cloneMaxProgress: 0,
            cloneProgress: 0,
        };
    },

    computed: {
        progressInPercenposte() {
            return 100 / this.cloneMaxProgress * this.cloneProgress;
        },

        repository() {
            return this.repositoryFactory.create('magefanblog_post');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.duplicate();
        },

        duplicate() {
            this.numberRangeService
                .reserve('post')
                .then(this.cloneParent)
                .then(this.verifyVariants);
        },

        async cloneParent(number) {
            const behavior = {
                cloneChildren: false,
                overwrites: {
                    title: `${this.post.title} ${this.$tc('global.default.copy')}`,
                    active: false,
                },
            };

            await this.repository.save(this.post, Context.api);
            const clone = await this.repository.clone(this.post.id, Shopware.Context.api, behavior);

            return { id: clone.id };
        },

        verifyVariants(duplicate) {
            this.getChildrenIds().then((ids) => {
                if (ids.length <= 0) {
                    this.$emit('clone-finish', { id: duplicate.id });
                    return;
                }

                this.cloningVariants = true;

                this.cloneProgress = 1;
                this.cloneMaxProgress = ids.length;

                this.duplicateVariant(duplicate, ids, () => {
                    this.cloningVariants = false;
                    this.$emit('clone-finish', { id: duplicate.id });
                });
            });
        },

        getChildrenIds() {
            const criteria = new Criteria(1, null);
/*            criteria.addFilter(
                Criteria.equals('parentId', this.product.id),
            );*/

            return this.repository
                .searchIds(criteria)
                .then((response) => {
                    return response.data;
                });
        },

        duplicateVariant(duplicate, ids, callback) {
            if (ids.length <= 0) {
                callback();
                return;
            }
            const id = ids.shift();

            const behavior = {
                overwrites: {
                  /*  parentId: duplicate.id,*/
                  /*  productNumber: `${duplicate.productNumber}.${this.cloneProgress}`,*/
                },
                cloneChildren: false,
            };

            this.repository
                .clone(id, Shopware.Context.api, behavior)
                .then(() => {
                    this.cloneProgress += 1;
                    this.duplicateVariant(duplicate, ids, callback);
                });
        },
    },
});
