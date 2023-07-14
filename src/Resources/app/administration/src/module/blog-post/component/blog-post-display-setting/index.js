/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-display-setting.html.twig';

const { Component, Mixin, Context } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();
const {Criteria} = Shopware.Data;

Component.register('blog-post-display-setting', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
    ],

    props: {
        post: {
            type: Object,
            required: true,
            default() {
                return {};
            },
        },
        isChangedLanguage: {
            type: String,
            default: false,
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

    watch: {
        isChangedLanguage () {
            this.initValues();
        }
    },

    created() {
        this.initValues();
        this.checkAuthorExist();

        if (!this.post.title){
            this.post.includeInRecent = true;
        }
    },

    data() {
        return {
            tags: [],
            tagsSelected: [],
            authorItem: {},
        };
    },

    computed: {
        getAdminUser() {
            const { currentUser } = Shopware.State.get('session');

            return currentUser;
        },

        authorRepository() {
            return this.repositoryFactory.create('magefanblog_author');
        },

        tagRepository() {
            return this.repositoryFactory.create('magefanblog_tag');
        },

        postRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        }

    },

    methods: {
        initValues() {
            this.getAllTags();
            this.getSelectedTags();
        },

        getAllTags() {
            const criteria = new Criteria();
            this.tagRepository.search(criteria, Shopware.Context.api).then((tags) => {
                const preparedTags = [];
                for (let tag of tags) {
                    preparedTags.push({value: tag.id, label: tag.title})
                }
                this.tags = preparedTags;
            })
        },

        getSelectedTags() {
            const criteria = new Criteria();
            criteria.addAssociation('postTags');
            criteria.addFilter(Criteria.equals('id', this.post.id));

            this.postRepository.search(criteria, Shopware.Context.api).then((post) => {
                const preparedSelectedTags = [];
                if  (typeof(post[0]) != "undefined") {
                    for (let tag of post[0].postTags) {
                        preparedSelectedTags.push(tag.id);
                    }
                }
                this.tagsSelected = preparedSelectedTags;
                this.$emit('tags', this.tagsSelected);
            })
        },

        updateSelectedTags(tags) {
            this.tagsSelected = tags;
            this.$emit('tags', this.tagsSelected)
        },

        checkAuthorExist() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('adminUserId', this.getAdminUser.id));
            this.authorRepository.search(criteria, Shopware.Context.api).then((author) => {
                if (author.total === 0 && !this.post.authorId){
                    this.authorItem = this.authorRepository.create(Shopware.Context.api);
                    this.authorItem.firstname = this.getAdminUser.firstName;
                    this.authorItem.lastname = this.getAdminUser.lastName;
                    this.authorItem.adminUserId = this.getAdminUser.id;
                    this.authorRepository.save(this.authorItem, Context.api).then((result) => {
                        const author = JSON.parse(result.config.data);
                        if (author.id){
                            this.post.authorId = author.id;
                        }
                    })
                }else if (!this.post.authorId){
                    this.post.authorId = author[0].id
                }
            })
        },
    }
});
