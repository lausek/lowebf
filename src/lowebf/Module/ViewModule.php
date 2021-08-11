<?php

namespace lowebf\Module;

class ViewModule extends Module {

    /* @var Twig */
	protected $twig;

	public function render(string $template, array $data) {}

	public function renderToString(string $template, array $data): ?string {}
}
