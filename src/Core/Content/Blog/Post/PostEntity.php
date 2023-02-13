<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post;

use DateTimeInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PostEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected ?string $title;

    /**
     * @var string|null
     */
    protected ?string $metaTitle;

    /**
     * @var string|null
     */
    protected ?string $metaKeywords;

    /**
     * @var string|null
     */
    protected ?string $metaDescription;

    /**
     * @var string|null
     */
    protected ?string $identifier;

    /**
     * @var string|null
     */
    protected ?string $ogTitle;

    /**
     * @var string|null
     */
    protected ?string $ogDescription;

    /**
     * @var string|null
     */
    protected ?string $ogImg;

    /**
     * @var string|null
     */
    protected ?string $ogType;

    /**
     * @var string|null
     */
    protected ?string $contentHeading;

    /**
     * @var string|null
     */
    protected ?string $content;

    /**
     * @var string|null
     */
    protected ?string $publishTime;

    /**
     * @var bool|null
     */
    protected ?bool $isActive;

    /**
     * @var int|null
     */
    protected ?int $position;

    /**
     * @var string|null
     */
    protected ?string $featuredImg;

    /**
     * @var string|null
     */
    protected ?string $featuredImgAlt;

    /**
     * @var string|null
     */
    protected ?string $authorId;

    /**
     * @var string|null
     */
    protected ?string $pageLayout;

    /**
     * @var string|null
     */
    protected ?string $layoutUpdateXml;

    /**
     * @var string|null
     */
    protected ?string $customTheme;

    /**
     * @var string|null
     */
    protected ?string $customLayout;

    /**
     * @var string|null
     */
    protected ?string $customLayoutUpdateXml;

    /**
     * @var string|null
     */
    protected ?string $customThemeFrom;

    /**
     * @var string|null
     */
    protected ?string $customThemeTo;

    /**
     * @var string|null
     */
    protected ?string $mediaGallery;

    /**
     * @var int|null
     */
    protected ?int $includeInRecent;

    /**
     * @var string|null
     */
    protected ?string $secret;

    /**
     * @var int|null
     */
    protected ?int $viewsCount;

    /**
     * @var int|null
     */
    protected ?int $isRecentPostsSkip;

    /**
     * @var int|null
     */
    protected ?int $commentsCount;

    /**
     * @var string|null
     */
    protected ?string $mediaId;

    /**
     * @var string|null
     */
    protected ?string $postMediaVersionId;

    /**
     * @var DateTimeInterface|null
     */
    protected $createdAt;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    /**
     * @param $metaTitle
     * @return void
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * @return string|null
     */
    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    /**
     * @param $metaKeywords
     * @return void
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        $desc = $this->metaDescription;
        if (!$desc) {
            $desc = $this->getContentHeading() ?: '';
            $desc = str_replace(['<p>', '</p>'], [' ', ''], $desc);
        }

        $desc = strip_tags($desc);
        if (mb_strlen($desc) > 200) {
            $desc = mb_substr($desc, 0, 200);
        }

        return trim($desc);
    }

    /**
     * @param $metaDescription
     * @return void
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param $identifier
     * @return void
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string|null
     */
    public function getOgTitle(): ?string
    {
        $title = $this->ogTitle;
        if (!$title) {
            $title = $this->getMetaTitle();
        }

        return $title ? trim($title) : '';
    }

    /**
     * @param $ogTitle
     * @return void
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;
    }

    /**
     * @return string|null
     */
    public function getOgDescription(): ?string
    {
        $desc = $this->ogDescription;
        if (!$desc) {
            $desc = $this->getMetaDescription();
        } else {
            $desc = strip_tags($desc);
            if (mb_strlen($desc) > 300) {
                $desc = mb_substr($desc, 0, 300);
            }
        }

        return trim(html_entity_decode($desc));
    }

    /**
     * @param $ogDescription
     * @return void
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;
    }

    /**
     * @return string|null
     */
    public function getOgImg(): ?string
    {
        $img = $this->ogImg;
        if (!$img) {
            $img = $this->getFeaturedImg();
        }

        return $img;
    }

    /**
     * @param $ogImg
     * @return void
     */
    public function setOgImg($ogImg)
    {
        $this->ogImg = $ogImg;
    }

    /**
     * @return string|null
     */
    public function getOgType(): ?string
    {
        $type = $this->ogType;
        if (!$type) {
            $type = 'article';
        }

        return trim($type);
    }

    /**
     * @param $ogType
     * @return void
     */
    public function setOgType($ogType)
    {
        $this->ogType = $ogType;
    }

    /**
     * @return string|null
     */
    public function getContentHeading(): ?string
    {
        return $this->contentHeading;
    }

    /**
     * @param $contentHeading
     * @return void
     */
    public function setContentHeading($contentHeading)
    {
        $this->contentHeading = $contentHeading;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getPublishTime(): ?string
    {
        return $this->publishTime;
    }

    /**
     * @param $publishTime
     * @return void
     */
    public function setPublishTime($publishTime)
    {
        $this->publishTime = $publishTime;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param $isActive
     * @return void
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getFeaturedImg(): ?string
    {
        return $this->featuredImg;
    }

    /**
     * @param $featuredImg
     * @return void
     */
    public function setFeaturedImg($featuredImg)
    {
        $this->featuredImg = $featuredImg;
    }

    /**
     * @return string|null
     */
    public function getFeaturedImgAlt(): ?string
    {
        return $this->featuredImgAlt;
    }

    /**
     * @param $featuredImgAlt
     * @return void
     */
    public function setFeaturedImgAlt($featuredImgAlt)
    {
        $this->featuredImgAlt = $featuredImgAlt;
    }

    /**
     * @return string|null
     */
    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    /**
     * @param $authorId
     * @return void
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * @return string|null
     */
    public function getPageLayout(): ?string
    {
        return $this->pageLayout;
    }

    /**
     * @param $pageLayout
     * @return void
     */
    public function setPageLayout($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * @return string|null
     */
    public function getLayoutUpdateXml(): ?string
    {
        return $this->layoutUpdateXml;
    }

    /**
     * @param $layoutUpdateXml
     * @return void
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        $this->layoutUpdateXml = $layoutUpdateXml;
    }

    /**
     * @return string|null
     */
    public function getCustomTheme(): ?string
    {
        return $this->customTheme;
    }

    /**
     * @param $customTheme
     * @return void
     */
    public function setCustomTheme($customTheme)
    {
        $this->customTheme = $customTheme;
    }

    /**
     * @return string|null
     */
    public function getCustomLayout(): ?string
    {
        return $this->customLayout;
    }

    /**
     * @param $customLayout
     * @return void
     */
    public function setCustomLayout($customLayout)
    {
        $this->customLayout = $customLayout;
    }

    /**
     * @return string|null
     */
    public function getCustomLayoutUpdateXml(): ?string
    {
        return $this->customLayoutUpdateXml;
    }

    /**
     * @param $customLayoutUpdateXml
     * @return void
     */
    public function setCustomLayoutUpdateXml($customLayoutUpdateXml)
    {
        $this->customLayoutUpdateXml = $customLayoutUpdateXml;
    }

    /**
     * @return string|null
     */
    public function getCustomThemeFrom(): ?string
    {
        return $this->customThemeFrom;
    }

    /**
     * @param $customThemeFrom
     * @return void
     */
    public function setCustomThemeFrom($customThemeFrom)
    {
        $this->customThemeFrom = $customThemeFrom;
    }

    /**
     * @return string|null
     */
    public function getCustomThemeTo(): ?string
    {
        return $this->customThemeTo;
    }

    /**
     * @param $customThemeTo
     * @return void
     */
    public function setCustomThemeTo($customThemeTo)
    {
        $this->customThemeTo = $customThemeTo;
    }

    /**
     * @return string|null
     */
    public function getMediaGallery(): ?string
    {
        return $this->mediaGallery;
    }

    /**
     * @param $mediaGallery
     * @return void
     */
    public function setMediaGallery($mediaGallery)
    {
        $this->mediaGallery = $mediaGallery;
    }

    /**
     * @return int|null
     */
    public function getIncludeInRecent(): ?int
    {
        return $this->includeInRecent;
    }

    /**
     * @param $includeInRecent
     * @return void
     */
    public function setIncludeInRecent($includeInRecent)
    {
        $this->includeInRecent = $includeInRecent;
    }

    /**
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param $secret
     * @return void
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return int|null
     */
    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    /**
     * @param $viewsCount
     * @return void
     */
    public function setViewsCount($viewsCount)
    {
        $this->viewsCount = $viewsCount;
    }

    /**
     * @return int|null
     */
    public function getIsRecentPostsSkip(): ?int
    {
        return $this->isRecentPostsSkip;
    }

    /**
     * @param $isRecentPostsSkip
     * @return void
     */
    public function setIsRecentPostsSkip($isRecentPostsSkip)
    {
        $this->isRecentPostsSkip = $isRecentPostsSkip;
    }

    /**
     * @return int|null
     */
    public function getCommentsCount(): ?int
    {
        return $this->commentsCount;
    }

    /**
     * @param $commentsCount
     * @return void
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }

    /**
     * @return string|null
     */
    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    /**
     * @param $mediaId
     * @return void
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return string|null
     */
    public function getPostMediaVersionId(): ?string
    {
        return $this->postMediaVersionId;
    }

    /**
     * @param $postMediaVersionId
     * @return void
     */
    public function setPostMediaVersionId($postMediaVersionId)
    {
        $this->postMediaVersionId = $postMediaVersionId;
    }
}
