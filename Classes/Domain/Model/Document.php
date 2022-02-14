<?php

namespace VV\T3meilisearch\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This Document should only be a DTO but needs to extend
 * the AbstractDomainObject in order to map the properties
 * property. Not sure why, but without it does not work.
 */
class Document extends AbstractDomainObject
{
    protected string $id = '';
    protected int $rootPageId = 0;
    protected string $title = '';
    protected string $url = '';
    protected string $content = '';
    protected array $_formatted = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getRootPageId(): int
    {
        return $this->rootPageId;
    }

    public function setRootPageId(int $rootPageId): void
    {
        $this->rootPageId = $rootPageId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = strip_tags($content);
    }

    public function setFormatted(array $_formatted): void
    {
        if ($_formatted['content']) {
            $this->setContent($_formatted['content']);
        }
    }

    public static function createFromTSFE(TypoScriptFrontendController $tsfe): Document
    {
        preg_match('/INDEX_CONTENT_START(.*)INDEX_CONTENT_END/s', $tsfe->content, $content);

        if (count($content) === 0) {
            // No marker comments are found so we take all from within the body
            preg_match('/<body>(.*?)<\/body>/s', $tsfe->content, $content);
        }

        $document = new Document();
        $document->setId(md5($tsfe->cObj->getRequest()->getUri()));
        $document->setRootPageId($tsfe->getSite()->getRootPageId() ?? 0);
        $document->setContent($content[0] ?? '');
        $document->setTitle($tsfe->page['title'] ?? '');
        $document->setUrl($tsfe->cObj->getRequest()->getUri());

        return $document;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'rootPageId' => $this->rootPageId,
            'title' => $this->title,
            'url' => $this->url,
            'content' => $this->content,
        ];
    }
}
