/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-clone-modal.html.twig';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('blog-category-clone-modal', {
    template,

    inject: ['repositoryFactory', 'numberRangeService'],

    props: {
        product: {
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
        progressInPercencategorye() {
            return 100 / this.cloneMaxProgress * this.cloneProgress;
        },
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        repository() {
            return this.repositoryFactory.create('magefanblog_category');
        },
        mediaUploadcategory() {
            return `blog-post-detail--${this.category.id}`;
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
                .reserve('category')
                .then(this.cloneParent)
                .then(this.verifyVariants);
        },

        async cloneParent(number) {
            const behavior = {
                cloneChildren: false,
                overwrites: {
                    productNumber: number.number,
                    name: `${this.category.title} ${this.$tc('global.default.copy')}`,
                    active: false,
                    mainVariantId: null,
                },
            };

            await this.repository.save(this.category, Context.api);
            const clone = await this.repository.clone(this.category.id, Shopware.Context.api, behavior);

            return {id: clone.id, productNumber: number.number};
        },

        verifyVariants(duplicate) {
            this.getChildrenIds().then((ids) => {
                if (ids.length <= 0) {
                    this.$emit('clone-finish', {id: duplicate.id});
                    return;
                }

                this.cloningVariants = true;

                this.cloneProgress = 1;
                this.cloneMaxProgress = ids.length;

                this.duplicateVariant(duplicate, ids, () => {
                    this.cloningVariants = false;
                    this.$emit('clone-finish', {id: duplicate.id});
                });
            });
        },

        getChildrenIds() {
            const criteria = new Criteria(1, null);
            criteria.addFilter(
                Criteria.equals('parentId', this.category.id),
            );

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
                    parentId: duplicate.id,
                    productNumber: `${duplicate.productNumber}.${this.cloneProgress}`,
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

        setMediaItem({targetId}) {
            this.category.mediaId = targetId;
        },

        setMediaFromSidebar(media) {
            this.category.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.category.mediaId = null;
        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },

        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },
    },
});
