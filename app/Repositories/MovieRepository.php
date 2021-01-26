<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class MovieRepository extends AbstractRepository
{
    public const MOVIEU_UPDATE_Value = 1;

    public function findMovieByField($field, $value)
    {
        $query = $this->model
            ->where($field,'=', $value);
        return $query->get();
    }

    public function updateMovie($movieId)
    {
        $this->model
            ->where('movie_id', $movieId)
            ->update(['a_mettre_a_jour' => 0]);
    }


}
