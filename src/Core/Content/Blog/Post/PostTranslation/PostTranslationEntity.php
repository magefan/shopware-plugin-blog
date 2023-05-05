<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\Blog\Core\Content\Blog\Post\PostTranslation;

use DateTimeInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class PostTranslationEntity extends TranslationEntity
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
}
