<?php

namespace lowebf\Module;

use lowebf\Environment;
use lowebf\Data\Post;

class PostModule extends Module
{
    /* @var int */
	    private $postsPerPage;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->postsPerPage = 15;
    }

    private function getPostPath(string $postId) : string
    {
        return $this->env->asAbsoluteDataPath("posts/$postId.md");
    }

    public function loadPage(int $page) : array {}

    public function load(string $postId) : Post
    {
        $path = $this->getPostPath($postId);
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
