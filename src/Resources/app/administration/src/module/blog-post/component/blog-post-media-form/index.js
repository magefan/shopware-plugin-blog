/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

import template from './blog-post-media-form.html.twig';

const {Component, Mixin} = Shopware;

Component.register('blog-post-media-form', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
    ],

    shortcuts: {
        'SYSTEMKEY+S': 'onSave',
        ESCAPE: 'onCancel',
    },

    props: {
        postId: {
            type: String,
            required: false,
            default: null,
        },
        post: {
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
    },

    provide() {
        return {
            openMediaSidebar: this.openMediaSidebar,
        };
    },

    data() {
        return {
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
            return this.placeholder(this.post, 'title');
        },

        postRepository() {
            return this.repositoryFactory.create('magefanblog_post');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        mediaUploadTag() {
            return `blog-post-detail--${this.post.id}`;
        },

        tooltipSave() {
            if (this.acl.can('blog_post.editor')) {
                const systemKey = this.$device.getSystemKey();

                return {
                    message: `${systemKey} + S`,
                    appearance: 'light',
                };
            }

            return {
                showDelay: 300,
                message: this.$tc('sw-privileges.tooltip.warning'),
                disabled: this.acl.can('blog_post.editor'),
                showOnDisabledElements: true,
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },
    },

    watch: {
        postId() {
            this.createdComponent();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.postId) {
                this.loadEntityData();
                return;
            }
        },

        loadEntityData() {
            this.isLoading = true;

            this.postRepository.get(this.postId).then((post) => {
                this.isLoading = false;
                this.post = post;
            });
        },

        setMediaItem({targetId}) {
            this.post.mediaId = targetId;
        },

        setMediaFromSidebar(media) {
            this.post.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.post.mediaId = null;
        },

        openMediaSidebar() {
            this.$parent.$parent.$parent.$refs.mediaSidebarItem.openContent();
        },

        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },

        onSave() {
            if (!this.acl.can('blog_post.editor')) {
                return;
            }

            this.isLoading = true;

            this.postRepository.save(this.post).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                if (this.postId === null) {
                    this.$router.push({name: 'blog.post.detail', params: {id: this.post.id}});
                    return;
                }

                this.loadEntityData();
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc(
                        'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                    ),
                });
                throw exception;
            });
        },

        onCancel() {
            this.$router.push({name: 'blog.post.index'});
        },
    },
});
