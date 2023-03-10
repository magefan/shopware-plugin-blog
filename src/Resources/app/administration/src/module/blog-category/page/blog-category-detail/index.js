/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-category-detail.html.twig';
import slug from "slug";
import path from "path";

const {Component, Mixin, Data: {Criteria}} = Shopware;

const {mapPropertyErrors} = Shopware.Component.getComponentHelper();

Component.register('blog-category-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    props: {
        id: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            category: null,
            rootCategoryBlog: {},
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.category, 'title');
        },

        categoryIsLoading() {
            return this.isLoading || this.category == null;
        },

        categoryMenuRepository() {
            return this.repositoryFactory.create('category');
        },

        categoryRepository() {
            return this.repositoryFactory.create('magefanblog_category');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        mediaUploadTag() {
            return `blog-category-detail--${this.category.id}`;
        },

        tooltipSave() {
            if (this.acl.can('blog_category.editor')) {
                const systemKey = this.$device.getSystemKey();

                return {
                    message: `${systemKey} + S`,
                    appearance: 'light',
                };
            }

            return {
                showDelay: 300,
                message: this.$tc('sw-privileges.tooltip.warning'),
                disabled: this.acl.can('blog_category.editor'),
                showOnDisabledElements: true,
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

        ...mapPropertyErrors('category', ['title']),
    },

    watch: {
        id() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.id) {
                this.loadEntityData();
                return;
            }

            this.category = this.categoryRepository.create();
        },

        loadEntityData() {
            this.categoryRepository.get(this.id).then((category) => {
                this.isLoading = false;
                this.category = category;
            }).catch(() => {
                this.isLoading = false;
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

        updateCategoryMenu() {
            this.getRootBlogCategory().then((rootCategory) => {
                this.categoryMenuRepository.get(this.category.id).then((category) => {
                    if (rootCategory[0].id && !category) {
                        const category = this.categoryMenuRepository.create();
                        category.id = this.category.id;
                        category.type = 'link';
                        category.parentId = rootCategory[0].id;
                        category.name = this.category.title
                        category.linkType = 'external';
                        category.externalLink = rootCategory[0].externalLink + '/category/' + this.category.identifier;
                        category.level = 3;
                        category.active = Boolean(this.category.isActive && this.category.includeInMenu);
                        this.categoryMenuRepository.save(category);
                    } else if(rootCategory[0].id && category) {
                        this.categoryMenuRepository.get(this.category.id).then((category) => {
                            category.id = this.category.id;
                            category.name = this.category.title
                            category.externalLink = rootCategory[0].externalLink + '/category/' + this.category.identifier;
                            category.level = 3;
                            category.active = Boolean(this.category.isActive && this.category.includeInMenu);
                            this.categoryMenuRepository.save(category);
                        });
                    }
                })
            });
        },

        getRootBlogCategory() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('name', 'Blog'));
            criteria.addFilter(Criteria.equals('linkType', 'external'));
            return this.categoryMenuRepository.search(criteria).then((result) => {
                return result;
            })
        },

        onSave() {
            this.isLoading = true;

            let identifier = this.category.identifier
            if (this.category.title) {
                if (identifier !== undefined && !this.isUrlValid(identifier)) {
                    identifier = slug(identifier, '-');
                } else {
                    identifier = slug(this.category.title, '-');
                }
            }

            this.category.identifier = identifier;
            this.categoryRepository.save(this.category).then(() => {
                this.updateCategoryMenu()
                this.isLoading = false;
                this.isSaveSuccessful = true;
                if (this.category.id) {
                    this.$router.push({name: 'blog.category.detail', params: {id: this.category.id}});
                }
            })
                .catch((exception) => {
                    this.isLoading = false;
                    this.isSaveSuccessful = false;
                    this.createNotificationError({
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                        ),
                    });
                    throw exception;
                });
        },

        onCancel() {
            this.$router.push({name: 'blog.category.list'});
        },

        isUrlValid(str) {
            return /^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/.test(str);
        },

        prepareIdentifier(str) {
            return str.replace(/ +/g, '-').toLowerCase();
        }
    }
});
