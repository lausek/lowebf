<?php

namespace lowebf\Module;

use lowebf\Data\Post;

class PostModule extends Module {

    /* @var int */
	private $postsPerPage;

	public function loadPage(int $page): ?array {}

	public function load(string $postId): ?Post {}
        
	public function save(Post $post) {}

	public function provideRssAndExit() {}
}
