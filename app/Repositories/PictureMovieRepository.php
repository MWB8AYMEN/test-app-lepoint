<?php


namespace App\Repositories;


class PictureMovieRepository extends AbstractRepository
{
    public function getPicturesMovieInfo($movieId)
    {
        $query = $this->model
            ->select('picture_id', 'url', 'width', 'height', 'mime_type', 'picture_type_picture_type_id')
            ->join('picture as p', 'picture_picture_id', '=', 'picture_id')
            ->where('movie_movie_id','=', $movieId);
        return $query->get();
    }
}
