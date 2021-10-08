<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\Post;
use lowebf\Error\FileNotFoundException;
use lowebf\Error\NotPersistableException;
use lowebf\Result;

class PostModule extends Module
{
    /** @var int */
	    private $postsPerPage;

    /** @var array|null */
	    private $posts = null;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $postsPerPage = $this->env->config()->lowebf()->getPostsPerPageAmount();

        $this->setPostsPerPage($postsPerPage);
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

    /**
     * @return Result<string>
     * */
    public function findPostPath(string $postId) : Result
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
        // TODO: this does not recognize newly created posts!
        if ($this->posts === null) {
            $postDirectory = $this->env->asAbsoluteDataPath("posts");
            $posts = $this->env->listDirectory($postDirectory)->unwrapOr([]);
            $this->posts = [];

            foreach ($posts as $postPath) {
                // TODO: how to handle other exceptions?
                try {
                    $this->posts[] = Post::loadFromFile($this->env, $postPath)->unwrap();
                } catch (NotPersistableException $e) {
                    // file has an unsupported extension and cannot be parsed. skipping.
                }
            }

            rsort($this->posts);
        }

        return $this->posts;
    }

    /**
     * @return Result<array<Post>>
     * */
    public function loadPage(int $pageNumber) : Result
    {
        if ($pageNumber <= 0) {
            return Result::error(new \Exception("invalid page number: $pageNumber"));
        }

        $posts = $this->loadPosts();
        $postsPerPage = $this->getPostsPerPage();
        $offset = ($pageNumber - 1) * $postsPerPage;
        $postsOnPage = array_slice($posts, $offset, $postsPerPage);

        return Result::ok($postsOnPage);
    }

    /**
     * @return Result<Post>
     * */
    public function load(string $postId) : Result
    {
        try {
            $path = $this->findPostPath($postId)->unwrap();
            return Post::loadFromFile($this->env, $path);
        } catch (\Exception $e) {
            return Result::error($e);
        }
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
