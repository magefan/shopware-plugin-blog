/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-tag-detail.html.twig';
import slug from "slug";

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('blog-tag-detail', {
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    shortcuts: {
        'SYSTEMKEY+S': {
            active() {
                return this.acl.can('blog_tag.editor');
            },
            method: 'onSave',
        },
        ESCAPE: 'onCancel',
    },

    props: {
        tagId: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            tag: null,
            isLoading: false,
            isSaveSuccessful: false,
            customFieldSets: null,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        identifier() {
            return this.placeholder(this.tag, 'title');
        },

        tagRepository() {
            return this.repositoryFactory.create('magefanblog_tag');
        },

        tooltipSave() {
            if (!this.acl.can('blog_tag.editor')) {
                return {
                    message: this.$tc('blog-tag.tooltip.warning'),
                    disabled: this.acl.can('blog_tag.editor'),
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
    },

    watch: {
        tagId() {
            this.loadEntityData();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadEntityData();
        },

        loadEntityData() {
            this.isLoading = true;

            this.tagRepository.get(this.$attrs.id, Shopware.Context.api, this.defaultCriteria)
                .then((currentTag) => {
                    this.tag = currentTag;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },
        saveFinish() {
            this.isSaveSuccessful = false;
        },

        saveTag() {
            this.isLoading = true;

            return new Promise((resolve) => {
                let identifier = this.tag.identifier
                if (this.tag.title){
                    if (identifier !== undefined && !this.isUrlValid(identifier)) {
                        identifier = slug(identifier, '-');
                    } else {
                        identifier = slug(this.tag.title, '-');
                    }
                }

                this.tag.identifier = identifier;
                this.tagRepository.save(this.tag).then(() => {
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                    resolve('success');
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
            return this.saveTag();
        },

        onCancel() {
            this.$router.push({name: 'blog.tag.index'});
        },

        isUrlValid(str) { return /^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/.test(str); },

        prepareIdentifier(str) {
            return str.replace(/ +/g, '-').toLowerCase();
        }
    },
});
