<?php

namespace App\Services;


use App\Repositories\MoviePersonRepository;
use App\Repositories\PictureMovieRepository;
use App\Repositories\MovieRepository;
use Illuminate\Support\Facades\Storage;

class DataMoviePersonService
{
    private const REDIS_STORAGE_KEY = 'persons';

    /**
     * @var RedisDriverStorage
     */
    private $redisDriverStorage;

    /**
     * @var MoviePersonRepository
     */
    private $moviePersonRepository;

    /**
     * @var PictureMovieRepository
     */
    private $pictureMovieRepository;

    /**
     * @var MovieRepository
     */
    private $movieRepository;


    /**
     * DataMoviePersonService constructor.
     * @param RedisDriverStorage $redisDriverStorage
     * @param MoviePersonRepository $moviePersonRepository
     * @param PictureMovieRepository $pictureMovieRepository
     * @param MovieRepository $movieRepository
     */
    public function __construct(RedisDriverStorage $redisDriverStorage, MoviePersonRepository $moviePersonRepository, PictureMovieRepository $pictureMovieRepository, MovieRepository $movieRepository)
    {
        $this->redisDriverStorage = $redisDriverStorage;
        $this->moviePersonRepository = $moviePersonRepository;
        $this->pictureMovieRepository = $pictureMovieRepository;
        $this->movieRepository = $movieRepository;
    }

    /**
     * @param string $nbrLines
     * @return array
     */
    public function findPersonsWithKwmLpNotNUl(string $nbrLines): array
    {
        $results = $this->moviePersonRepository->getPersonsWithKwmLpNotNUll($nbrLines);
        $selectedPersons = [];

        if ($results->count() > 0) {
            $this->redisDriverStorage->setKey(self::REDIS_STORAGE_KEY);
            foreach ($results as $result) {
                $attributes = $result->getAttributes();
                $this->redisDriverStorage->add($attributes['person_person_id']);
            }

            $selectedPersons = $this->redisDriverStorage->get(0, $nbrLines - 1);
        }

        return $selectedPersons;
    }

    /**
     * @param array $personIds
     * @return array
     */
    public function generateDataPersons(array $personIds): array
    {
        $lines = [];
        $movies = [];
        foreach ($personIds as $value) {
            $data = $this->moviePersonRepository->getMoviesPersonInfo($value);
            if ($data) {
                foreach ($data as $raw) {
                    $attributesRaw = $raw->getAttributes();

                    $lines[$attributesRaw['person_id']]['id'] = $attributesRaw['person_id'];
                    $lines[$attributesRaw['person_id']]['idPerson'] = $attributesRaw['person_id'];
                    $lines[$attributesRaw['person_id']]['suppression'] = 0;
                    $lines[$attributesRaw['person_id']]['type'] = 'info-cine';
                    $lines[$attributesRaw['person_id']]['title'] = $attributesRaw['name_person'];
                    $lines[$attributesRaw['person_id']]['content']['nom'] = $attributesRaw['name_person'];
                    $lines[$attributesRaw['person_id']]['content']['url_dbpedia'] = $attributesRaw['url_dbpedia'];
                    $lines[$attributesRaw['person_id']]['content']['lieu_naissance'] = $attributesRaw['lieu_naissance'];
                    $lines[$attributesRaw['person_id']]['content']['nationalite'] = $attributesRaw['nationalite'];
                    $lines[$attributesRaw['person_id']]['content']['commentaire'] = $attributesRaw['commentaire'];
                    $lines[$attributesRaw['person_id']]['content']['profession'] = $attributesRaw['profession'];
                    $lines[$attributesRaw['person_id']]['content']['date_naissance'] = $attributesRaw['date_naissance'];
                    $lines[$attributesRaw['person_id']]['content']['photo'] = $attributesRaw['photo'];

                    $movies[] = $attributesRaw['movie_id'];
                    $lines[$attributesRaw['person_id']]['content']['movies'][$attributesRaw['movie_id']] = self::generateMovieInfo($attributesRaw);

                    $picturesMovie = $this->pictureMovieRepository->getPicturesMovieInfo($attributesRaw['movie_id']);

                    foreach ($picturesMovie as $pictureMovie) {
                        $infoPicture = $pictureMovie->getAttributes();
                        $lines[$attributesRaw['person_id']]['content']['movies'][$attributesRaw['movie_id']]['content']['pictures'][] =
                            self::generatePictureMovie($infoPicture);
                    }

                    $lines[$attributesRaw['person_id']]['content']['movies'][$attributesRaw['movie_id']]['content']['fonctions'][] =
                        self::generateFunctionsData($attributesRaw);

                }

                Storage::disk('public')->put($value . '.json', response()->json($lines[$value]));
            }
        }

        foreach ($movies as $movieId) {
            $this->movieRepository->updateMovie($movieId);
        }

        return $lines;
    }

    /**
     * @param array $attributesRaw
     * @return array
     */
    private static function generateMovieInfo(array $attributesRaw): array
    {
        return ['title' => $attributesRaw['original_title'],
            'content' =>
                [
                    'brightcove_id' => $attributesRaw['brightcove_id'],
                    'product_title' => $attributesRaw['product_title'],
                    'age_limit' => $attributesRaw['age_limit'],
                    'description' => $attributesRaw['description'],
                    'movie_duration' => $attributesRaw['movie_duration'],
                    'imdb_id' => $attributesRaw['imdb_id'],
                    'original_title' => $attributesRaw['original_title'],
                    'premiere' => $attributesRaw['premiere'],
                    'production_year' => $attributesRaw['production_year'],
                    'search_engine' => $attributesRaw['search_engine'],
                    'official_website' => $attributesRaw['official_website'],
                ]];
    }

    /**
     * @param array $infoPicture
     * @return array
     */
    private static function generatePictureMovie(array $infoPicture): array
    {
        return [
            'title' => $infoPicture['picture_id'],
            'content' => [
                'name' => basename($infoPicture['url']),
                'url' => $infoPicture['url'],
                'width' => $infoPicture['width'],
                'height' => $infoPicture['height'],
                'mime_type' => $infoPicture['mime_type'],
                'picture_type' => $infoPicture['picture_type_picture_type_id']
            ]
        ];
    }

    /**
     * @param array $attributesRaw
     * @return array
     */
    private static function generateFunctionsData(array $attributesRaw): array
    {
        return [
            'title' => $attributesRaw['person_type_id'],
            'content' => [
                'id' => $attributesRaw['person_type_id'],
                'lebelle' => $attributesRaw['name_person_type'],
            ]
        ];
    }

    /**
     * @param $personIds
     */
    public function deleteProcessedPersonsIds($personIds): void
    {
        foreach ($personIds as $personId) {

            $this->redisDriverStorage->remove($personId);
        }
    }


}
