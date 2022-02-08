<?php

namespace VV\T3meilisearch\Domain\Model;

use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Request;

class Document
{
    protected int $id = 0;
    protected string $title = '';
    protected string $link = '';
    protected string $content = '';

    public function getUid(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
        $this->content = $content;
    }

    public static function createFromRequest(Request $request, Response $response): Document
    {
        preg_match('/<body>(.*?)<\/body>/s', $response->getBody()->__toString(), $content);

        $document = new Document();
        $document->setId($GLOBALS['TSFE']->page['uid'] ?? 0);
        $document->setContent($content[0] ?? '');
        $document->setTitle($GLOBALS['TSFE']->page['title'] ?? '');
        $document->setLink($request->getUri());

        return $document;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'content' => $this->content,
        ];
    }
}
