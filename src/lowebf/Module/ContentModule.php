<?php

namespace lowebf\Module;

class ContentModule extends Module {
	public function load(string $contentUnitId): ?ContentUnit {}

	public function save(string $contentUnitId, ContentUnit $contentUnit) {}
}
