<?php

namespace VV\T3meilisearch\Domain\Model;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Request;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * This Document should only be a DTO but needs to extend
 * the AbstractDomainObject in order to map the properties
 * property. Not sure why, but without it does not work.
 */
class Document extends AbstractDomainObject
{
    protected string $id = '';
    protected int $pageUid = 0;
    protected string $title = '';
    protected string $link = '';
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

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function setPageUid(int $pageUid): void
    {
        $this->pageUid = $pageUid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
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

    public static function createFromRequest(Request $request, Response $response): Document
    {
        preg_match('/<body>(.*?)<\/body>/s', $response->getBody()->__toString(), $content);

        $document = new Document();
        $document->setId(md5($request->getUri()));
        $document->setPageUid($GLOBALS['TSFE']->page['uid'] ?? 0);
        $document->setContent($content[0] ?? '');
        $document->setTitle($GLOBALS['TSFE']->page['title'] ?? '');
        $document->setLink($request->getUri());

        return $document;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pageUid' => $this->pageUid,
            'title' => $this->title,
            'link' => $this->link,
            'content' => $this->content,
        ];
    }
}
