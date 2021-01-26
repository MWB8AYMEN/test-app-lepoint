<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{
    /**
     * @param $id
     * @return Model|null
     */
    public function findOneById($id): ?Model;

    /**
     * @param array $ids
     * @return Collection
     */
    public function findByIds(array $ids): Collection;

    /**
     * @return Collection
     */
    public function findAll(): Collection;
}
