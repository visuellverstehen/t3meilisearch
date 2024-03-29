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
    protected int $crdate = 0;
    protected int $rootPageId = 0;
    protected string $title = '';
    protected string $url = '';
    protected string $content = '';
    protected string $type = '';
    protected array $_formatted = [];
    protected int $languageId = 0;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
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
        $this->content = strip_tags($content, '<em>');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = strtolower($type);
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function setFormatted(array $_formatted): void
    {
        if ($_formatted['content']) {
            $this->setContent($_formatted['content']);
        }
    }

    public static function createFromTSFE(TypoScriptFrontendController $tsfe): Document
    {
        preg_match('/<!--\s?INDEX_CONTENT_START(.*)INDEX_CONTENT_STOP\s?-->/s', $tsfe->content, $content);

        if (count($content) === 0) {
            // No marker comments are found so we take all from within the body
            preg_match('/<body>(.*?)<\/body>/s', $tsfe->content, $content);
        }

        // Remove code that shouldn't be indexed
        $content = preg_replace('/<select(.*?)<\/select>/s', '', $content);
        $content = preg_replace('/<input(.*?)\/>/s', '', $content);
        $content = preg_replace('/<label(.*?)\/label>/s', '', $content);
        $content = preg_replace('/<svg(.*?)\/svg>/s', '', $content);

        // Remove query and fragments from URL
        $uri = $tsfe->cObj->getRequest()->getUri();
        $url = $uri->getScheme() . '://' . $uri->getAuthority();
        $path = $uri->getPath();
        if ($path !== '' && ! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        $url .= $path;

        // Remove trailing slashes from URL
        $url = rtrim($url, '/');

        $document = new Document();
        $document->setId(md5($url));
        $document->setRootPageId($tsfe->getSite()->getRootPageId() ?? 0);
        $document->setContent(implode(PHP_EOL, $content ?? []));
        $document->setType('page');
        $document->setTitle($tsfe->page['title'] ?? '');
        $document->setUrl($url);
        $document->setCrdate($tsfe->page['crdate']);
        $document->setLanguageId($tsfe->getLanguage()->getLanguageId() ?? 0);

        return $document;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'crdate' => $this->crdate,
            'rootPageId' => $this->rootPageId,
            'title' => $this->title,
            'url' => $this->url,
            'content' => $this->content,
            'type' => $this->type,
            'languageId' => $this->languageId,
        ];
    }
}
