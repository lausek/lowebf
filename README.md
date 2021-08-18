# lowebf

Is a microframework for creating simple websites.

## Features

- [X] No database connections
- [X] No models
- [X] No routing layer

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
