# t3meilisearch

## Preparation

Install Meilisearch yourself and make sure it is running. Also make sure you get the host address the server is reachable at, because you will need to configure the extension with that host. Otherwhise the extension cannot communicate with Meilisearch.

You will also need `poppler-utils` in order to index PDF files.

## Installation

You can easily install the extension by using Composer:

```bash
composer require visuellverstehen/t3meilisearch
```

You may have to activate the extension in TYPO3 10.4, but since TYPO3 11.5 this is not required anymore.

## Configuration

Log into TYPO3 as an admin or systemmaintainer and go to the settings module. Adjust the configure for t3meilisearch by setting the following fields:

### Host

The address the Meilisearch server is reachable at. Optionaly including the port.

Example:

```
host=127.0.0.1:7700
```

### Api

The key to authenticate the requests

Example:

```
apiKey=masterKey
```

### Target page uid

The uid of the page where the main plugin lives (see [Usage](#usage)).

Example:

```
targetPid=56
```

### Index

You an define a custom index by replacing the default value provided by t3meilisearch.

Example:

```
index=content
```

Thats pretty much it. The extension will then index pages and pdfs after they've been cached.

<a name="usage"></a>
## Usage

It is recommended to create a new page which can be hidden but not disabled. Insert the plugin »Searchform with results (Pi1)«. This will show a search form and also results if available.

You can also use the »Searchform (Pi2)« plugin to only display a search form wich will redirect to the target page configured in the settings module. You may insert this plugin hardcoded into e.g. the footer like this:

```html
 <f:cObject typoscriptObjectPath="tt_content.list.20.t3meilisearch_pi2" />
```

### Parsing content

By default everything inbetween `<body></body>` is beeing indexed. You can limit this by using two HTML comments just like indexed_search does. If you use multiple blocks, only the first will be index.

```html
<!-- INDEX_CONTENT_START -->
<p>This content will be indexed</p>
<!-- INDEX_CONTENT_STOP -->

<p>This content will NOT be indexed</p>
```

### Overriding templates

Because it's a basic TYPO3 extension build using Extbase, you can easily override the default templates and use you own. First configure an additional location for your templates and partials in your TypoScript for TYPO3 to look in:

```
plugin.tx_t3meilisearch {
    view {
        templateRootPaths {
            5000 = EXT:custom_extension/Resources/Private/Templates/
        }
        partialRootPaths {
            5000 = EXT:custom_extension/Resources/Private/Partials/
        }
    }
}
```

Next you can copy and modify the default templates the way you like. It is important to replicate the namespace structure, otherwhise TYPO3 cannot find the templates and partials.

### Exclude pages from indexing

You can disable the »Include in Search« checkbox when editing a page to prevent a page beeing indexed.

### Dropping the index

Want a clear start? You can simply execute a HTTP request to drop the index:

```bash
curl -H 'Authorization: Bearer yourApiKey' -X DELETE 'http://localhost:7700/indexes/pages'
```

A new index with the name configured in `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3meilisearch']['index']` will be created by t3meilisearch.

### Removing specific documents

When there are old or unwanted results, you can easily remove the documents by executing a HTTP request. First find the `id` of the document by searching through the Meilisearch dashboard. Then you can execute the following request to delete a document:

```bash
curl -H 'Authorization: Bearer yourApiKey' -X DELETE 'http://localhost:7700/indexes/pages/documents/:id'
```

### Add sorting option

By default t3meilisearch sorts the results by crdate (filetime for PDFs) descending. You can add simple sorting options by adding a select:

```html
<select name="sorting">
    <option selected disabled>Sorting</option>
    <option value="crdate_desc" {f:if(condition: '{queryParams.sorting} === "crdate_desc" || !{queryParams.sorting}', then: 'selected')}>New first</option>
    <option value="crdate_asc" {f:if(condition: '{queryParams.sorting} === "crdate_asc"', then: 'selected')}>Old first</option>
</select>
```

It is important that the sorting value is passed by the query key called `sorting`. The value is composed by `column-to-sort_direction-to-sort`. If you want more complex sorting you have to do it yourself.

### Add type filtering

By default we add a two type to documents. `page` for normale page content and `pdf` for PDF files. You could index records yourself and add custom types. Filtering for types can be achieved by passing a `types` parameter:

```html
<input class="filter__input" type="checkbox" id="type-page" name="types[]" value="page" {f:if(condition: '"page" == {type}', then: 'checked') -> f:for(each: '{queryParams.types}', as: 'type')}>
<label class="filter__label" for="type-page">Allgemein</label>
```

---

Meilisearch has an easy API to use: [docs.meilisearch.com](https://docs.meilisearch.com/reference/api/)
