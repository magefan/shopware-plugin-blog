/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-detail.html.twig';
import slug from "slug";

const { Component, Mixin,Context, Data: { Criteria } } = Shopware;

Component.register('blog-post-detail', {
    template,

    inject: [
        'repositoryFactory',
        'acl',
        'customFieldDataProviderService',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('blog_post.editor');
            },
            method: 'onSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        id: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            post: null,
            categories: null,
            tags: null,
            isLoading: false,
            isSaveSuccessful: false,
            customFieldSets: null,
            tagItem: {},
            categoryItem: {},
            existTag: false,
            existCategory: false,
            isChangedLanguage: Shopware.Context.api.languageId,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.post, 'title');
        },

        optionRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        },

        postRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        },

        postCategoryRepository() {
            return this.repositoryFactory.create('magefanblog_post_category');
        },

        postTagRepository() {
            return this.repositoryFactory.create('magefanblog_post_tag');
        },

        tooltipSave() {
            if (!this.acl.can('blog_post.editor')) {
                return {
                    message: this.$tc('blog-post.tooltip.warning'),
                    disabled: this.acl.can('blog_post.editor'),
                    showOnDisabledElements: true,
                };
            }

            const systemKey = this.$device.getSystemKey();

            return {
                message: `${systemKey} + S`,
                appearance: 'light',
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },

        defaultCriteria() {
            return new Criteria(this.page, this.limit);
        },

        useNaturalSorting() {
            return this.sortBy === 'post.title';
        },

        showCustomFields() {
            return this.post && this.customFieldSets && this.customFieldSets.length > 0;
        },
    },

    watch: {
        id() {
            this.loadEntityData();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (Shopware.Context.api.languageId !== Shopware.Context.api.systemLanguageId) {
                Shopware.State.commit('context/setApiLanguageId', Shopware.Context.api.languageId)
            }

            if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                Shopware.State.commit('context/resetLanguageToDefault');
            }

            this.loadEntityData();
        },

        getTags(tags) {
            this.tags = tags
        },

        getCategories(categories) {
            this.categories = categories
        },

        loadEntityData() {
            this.isLoading = true;

            this.postRepository.get(this.id, Shopware.Context.api, this.defaultCriteria)
                .then((currentPost) => {
                    this.post = currentPost;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },

        onChangeLanguage(languageId) {

            Shopware.State.commit('context/setApiLanguageId', languageId);

            this.isChangedLanguage = languageId;
            this.loadEntityData();
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        },

        savePost() {
            this.isLoading = true;

            return new Promise((resolve) => {
                let identifier = this.post.identifier
                if (this.post.title) {
                    if (identifier !== undefined && !this.isUrlValid(identifier)) {
                        identifier = slug(identifier, '-');
                    } else {
                        identifier = slug(this.post.title, '-');
                    }
                    this.post.identifier = identifier;
                }

                this.updateCategories();
                this.updateTags();
                this.postRepository.save(this.post, Context.api).then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                    if (this.post.id) {
                        this.$router.push({name: 'blog.post.detail', params: {id: this.post.id}});
                    }
                }).catch((exception) => {
                    this.isLoading = false;
                    this.isSaveSuccessful = false;
                    this.createNotificationError({
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                        ),
                    });
                    throw exception;
                });
            });
        },

        onSave() {
            return this.savePost();
        },

        updateCategories() {
            if (this.categories) {
                for (const category of this.categories) {
                    this.checkCategoryExists(category).then((isCategoryNotExist) => {
                        if (this.existCategory) {
                            this.categoryItem = this.postCategoryRepository.create(Shopware.Context.api);
                            this.categoryItem.categoryId = category;
                            this.categoryItem.postId = this.post.id;
                            this.postCategoryRepository.save(this.categoryItem, Context.api).then(() => {

                            }).catch((response) => {
                                //  resolve(response);
                            });
                        }
                    });
                }
                const criteria = new Criteria();
                criteria.addFilter(Criteria.equals('postId', this.post.id));
                this.postCategoryRepository.search(criteria, Shopware.Context.api).then((relations) => {

                    if (relations) {
                        for (const relation of relations) {
                            if (!this.categories.includes(relation.categoryId)) {
                                this.postCategoryRepository.delete(relation.id, Context.api).then(() => {
                                })
                            }
                        }
                    }
                })
            }
        },

        updateTags() {
            if (this.tags) {
                for (const tag of this.tags) {
                    this.checkTagExists(tag).then((isTagNotExist) => {
                        if (isTagNotExist) {
                            this.tagItem = this.postTagRepository.create();
                            this.tagItem.tagId = tag;
                            this.tagItem.postId = this.post.id;
                            this.postTagRepository.save(this.tagItem).then(() => {
                            }).catch((response) => {
                                //  resolve(response);
                            });
                        }
                    });

                }
            }
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('postId', this.post.id));
            this.postTagRepository.search(criteria).then((relations) => {
                if (relations) {
                    for (const relation of relations) {
                        if (!this.tags.includes(relation.tagId)) {
                            this.postTagRepository.delete(relation.id, Context.api).then(() => {})
                        }
                    }
                }
            })
        },

        onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        },

        onCancel() {
            this.$router.push({name: 'blog.post.index'});
        },

        isUrlValid(str) {
            return /^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/.test(str);
        },

        checkCategoryExists(category) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('categoryId', category));
            criteria.addFilter(Criteria.equals('postId', this.post.id));

            return this.postCategoryRepository.search(criteria, this.context).then((response) => {
                return this.existCategory = (response.total === 0);
            });
        },

        checkTagExists(tag) {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('postId', this.post.id));
            criteria.addFilter(Criteria.equals('tagId', tag));

            return this.postTagRepository.search(criteria, this.context).then((response) => {
                return this.existTag = (response.total === 0);
            });
        },
    },
});
