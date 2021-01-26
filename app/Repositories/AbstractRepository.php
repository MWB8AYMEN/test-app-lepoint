<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var Model
     */
    protected $model;

    /**
     * AbstractRepository constructor.
     * @param string $modelClass
     */
    public function __construct(?string $modelClass = null)
    {
        $this->modelClass = $modelClass ?: self::guessModelClass();
        $this->model = app($this->modelClass);
    }

    private static function guessModelClass(): string
    {
        //dump(preg_replace('/(.+)\\\\Repositories\\\\(.+)Repository$/m', '$1\Models\\\$2', static::class));exit;
        return preg_replace('/(.+)\\\\Repositories\\\\(.+)Repository$/m', '$1\Models\\\$2', static::class);
    }

    /**
     * @param $id
     * @return Model|null
     */
    public function findOneById($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param $id
     * @return Model|null
     */
    public function findOneByfiel($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function findByIds(array $ids): Collection
    {
        return $this->model->whereIn($this->model->getKeyName(), $ids)->get();
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model->all();
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

}
