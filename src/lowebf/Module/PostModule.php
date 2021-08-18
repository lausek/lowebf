<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\Post;
use lowebf\Error\FileNotFoundException;

class PostModule extends Module
{
    /** @var int */
    const DEFAULT_POSTS_PER_PAGE = 15;

    /** @var int */
	    private $postsPerPage;

    /** @var array|null */
	    private $posts = null;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->setPostsPerPage(self::DEFAULT_POSTS_PER_PAGE);
    }

    public function getMaxPage() : int
    {
        return (int)ceil($this->getPostCount() / (float)$this->getPostsPerPage());
    }

    public function getPostsPerPage() : int
    {
        return $this->postsPerPage;
    }

    public function getPostCount() : int
    {
        return count($this->loadPosts());
    }

    public function getPostPath(string $postId, string $fileExtension = "md") : string
    {
        return $this->env->asAbsoluteDataPath("posts/$postId.$fileExtension");
    }

    public function findPostPath(string $postId) : ?string
    {
        $postDirectory = $this->env->asAbsoluteDataPath("posts");
        return $this->env->findWithoutFileExtension($postDirectory, $postId);
    }

    public function setPostsPerPage(int $postsPerPage)
    {
        $this->postsPerPage = $postsPerPage;
    }

    public function &loadPosts() : array
    {
        if ($this->posts === null) {
            $postDirectory = $this->env->asAbsoluteDataPath("posts");
            $this->posts = [];

            foreach ($this->env->listDirectory($postDirectory) as $postPath) {
                $this->posts[] = Post::loadFromFile($this->env, $postPath);
            }

            rsort($this->posts);
        }

        return $this->posts;
    }

    public function loadPage(int $pageNumber) : array
    {
        if ($pageNumber <= 0) {
            throw new \Exception("invalid page number: $pageNumber");
        }

        $posts = $this->loadPosts();
        $postsPerPage = $this->getPostsPerPage();
        $offset = ($pageNumber - 1) * $postsPerPage;
        $postsOnPage = array_slice($posts, $offset, $postsPerPage);

        return $postsOnPage;
    }

    public function load(string $postId) : Post
    {
        $path = $this->findPostPath($postId);

        if ($path === null) {
            throw new FileNotFoundException("$postId");
        }

        return Post::loadFromFile($this->env, $path);
    }

    public function loadOrCreate(string $postId) : Post
    {
        try {
            return $this->load($postId);
        } catch (\Throwable $e) {
            $path = $this->getPostPath($postId);
            return Post::loadFromFileOrCreate($this->env, $path);
        }
    }

    public function delete(string $postId) {}

    public function provideRssAndExit() {}
}
