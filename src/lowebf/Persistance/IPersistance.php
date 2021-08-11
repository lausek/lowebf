<?php

namespace lowebf\Persistance;

public interface IPersistance {

	public function load(string $path): array;

	public function save(string $path, array $data);

}
