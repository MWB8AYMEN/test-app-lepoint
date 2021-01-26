<?php


namespace App\Repositories;

class MoviePersonRepository extends AbstractRepository
{
    public const MOVIE_UPDATE_VALUE= 1;

    public function getPersonsWithKwmLpNotNUll()
    {
        $query = $this->model
            ->select('person_person_id')
            ->join('person as p', 'p.person_id', '=', 'person_person_id')
            ->join('movie as m', 'm.movie_id', '=', 'movie_movie_id')
            ->whereRaw('id_kwm_lp is not null')
            ->where('m.a_mettre_a_jour','=', self::MOVIE_UPDATE_VALUE)
            ->groupby('person_person_id');
        return $query->get();
    }

    public function getMoviesPersonInfo($personId)
    {
        $query = $this->model
            ->select('p.person_id', 'p.name_person', 'p.url_dbpedia', 'p.lieu_naissance', 'p.nationalite', 'p.commentaire', 'p.profession', 'p.date_naissance', 'p.photo',
                 'm.movie_id', 'm.original_title', 'm.brightcove_id', 'm.product_title', 'm.age_limit', 'm.description', 'm.movie_duration', 'm.imdb_id', 'm.premiere', 'm.production_year', 'm.search_engine', 'm.official_website', 'm.official_website',
            'pt.person_type_id', 'pt.name_person_type'
                )
            ->join('person as p', 'p.person_id', '=', 'person_person_id')
            ->join('movie as m', 'm.movie_id', '=', 'movie_movie_id')
            ->join('person_type as pt', 'pt.person_type_id','=', 'person_type_person_type_id')
            ->whereRaw('id_kwm_lp is not null')
            ->where('person_person_id','=', $personId)
            ->where('m.a_mettre_a_jour','=', self::MOVIE_UPDATE_VALUE);
        return $query->get();
    }


}
