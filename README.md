# lowebf

`lowebf` is a microframework for creating simple websites.
It provides clean interfaces to separate your site's logic and content.
The `lowebf\Environment` class offers modules each solving a specific use case:

| Name | Description |
|---|----|
| Cache | Write files to the site's caching directory |
| Config | Access configuration values |
| Content | Read a JSON, YAML, or Markdown file |
| Download | Send a downloadable file |
| Post | Read a news entry from file |
| Route | Generate url for static files |
| View | Render a page template |

## Features

- [X] Embed videos in markdown
- [X] No database connections
- [X] No models
- [X] No routing layer

## Example

```php
composer require lausek/lowebf
composer update
```

```php
<?php

// example implementation of `site/public/index.php`.
// your document root should be set to `site/public` to avoid
// request escapes into other directories.

// this only works if the vendor directory was added to 
// the include path inside `php.ini`.
require "autoload.php";

// create the default instance of our environment
$env = lowebf\Environment::getInstance();

// load a specific post page from your `data/posts` directory.
// by default, 15 posts are displayed in one page.
$pageNumber = $_GET["p"] ?? 1;
$page = $env->posts()->loadPage($pageNumber);
$maxPage = $env->posts()->getMaxPage();

// render the overview with the selected page
$env->view()->render("index.html",
    [
        "entries" => $page,
        "pageCurrent" => $pageNumber,
        "pageMax" => $maxPage,
    ]
);
```

## Directory structure

- `cache/`
    - `thumbs/`: Thumbnails of posts
    - `twig/`: Template caching for Twig
- `data/`
    - `content/`: Miscellaneous content data
    - `download/`: Files available for download
    - `media/`
        - `img/`: Images: png, jpeg, gif
        - `vid/`: Videos: mp4, avi
        - `misc/`: Other file formats like: pdf, json
    - `posts/`: Frequently updated news in Markdown format
    - `config.yaml`: General configuration for the site
- `site/`
    - `css/`:
    - `img/`:
    - `js/`:
    - `public/`: Accessible PHP files
        - `route.php`: Used for providing all sorts of static files
    - `template/`: Twig template directory

> **Note:** Most directories and files are not required if you do not need them.
