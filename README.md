# t3meilisearch

## Preparation

## Installation

You can easily install the extension by using Composer:

```bash
composer require visuellverstehen/t3meilisearch
```

You may have to activate the extension, but since TYPO3 11.5 this is not required anymore.

## Configuration

Go to the settings module and configure the extension by setting:

### Host

The address the Meilisearch server is reachable

### Api

The key to authenticate the requests

### Target page uid

The uid where the main plugin lives (see [Usage](#usage)).

<a name="usage"></a>
## Usage

It is recommended to create a new page which can be hidden but not disabled. Insert the plugin »Searchform with results (Pi1)«. This will show a search form and also results if available.

### Overriding templates

…
