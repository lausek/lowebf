<?php

namespace lowebf\Persistance;

interface IPersistance {

	public function load(string $path): array;

	public function save(string $path, array $data);

}
