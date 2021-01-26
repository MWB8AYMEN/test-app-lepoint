<?php

namespace Tests\Unit\Services;

use App\Models\MoviePerson;
use App\Models\PictureMovie;
use App\Repositories\MoviePersonRepository;
use App\Repositories\MovieRepository;
use App\Repositories\PictureMovieRepository;
use App\Services\RedisDriverStorage;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\DataMoviePersonService;

class DataMoviePersonServiceTest extends TestCase
{
    /**
     * @var RedisDriverStorage|MockObject
     */
    private $redisDriverStorageMock;

    /**
     * @var MoviePersonRepository|MockObject
     */
    private $moviePersonRepositoryMock;

    /**
     * @var PictureMovieRepository|MockObject
     */
    private $pictureMovieRepositoryMock;

    /**
     * @var MovieRepository|MockObject
     */
    private $movieRepositoryMock;

    /**
     * @var DataMoviePersonService
     */
    private $dataMoviePersonService;


    public function setUp(): void
    {
        parent::setUp();

        $this->redisDriverStorageMock = $this->createMock(RedisDriverStorage::class);
        $this->moviePersonRepositoryMock = $this->createPartialMock(MoviePersonRepository::class, ['getMoviesPersonInfo']);
        $this->pictureMovieRepositoryMock = $this->createMock(PictureMovieRepository::class);
        $this->movieRepositoryMock = $this->createMock(MovieRepository::class);
        $this->dataMoviePersonService = new DataMoviePersonService($this->redisDriverStorageMock, $this->moviePersonRepositoryMock, $this->pictureMovieRepositoryMock, $this->movieRepositoryMock);
    }

    public function testGenerateDataPersons(): void
    {
        $personIds = [1];

        $this->moviePersonRepositoryMock->method('getMoviesPersonInfo')->with($personIds[0])->willReturn($this->getPeronsMovies());
        $this->pictureMovieRepositoryMock->method('getPicturesMovieInfo')->with(4479)->willReturn($this->getPictureMovie());

        $dataGenerated = $this->dataMoviePersonService->generateDataPersons($personIds);

        $this->assertCount(1, $dataGenerated);
        $this->assertCount(1, $dataGenerated[1]['content']['movies']);
    }

    private function getPeronsMovies(): Collection
    {
        $personMovie = new MoviePerson();
        $personMovie->setAttribute('person_id', 1);
        $personMovie->setAttribute('name_person', "Richard Anconina");
        $personMovie->setAttribute('url_dbpedia', "http://fr.dbpedia.org/page/Richard_Anconina");
        $personMovie->setAttribute('lieu_naissance', "France");
        $personMovie->setAttribute('nationalite', null);
        $personMovie->setAttribute('profession', "Acteur");
        $personMovie->setAttribute('commentaire', "Richard Anconina est un acteur français.");
        $personMovie->setAttribute('date_naissance', "1953-01-28");
        $personMovie->setAttribute('photo', "2511_Richard_Anconina.jpg");
        $personMovie->setAttribute('movie_id', 4479);
        $personMovie->setAttribute('original_title', "Camping 2");
        $personMovie->setAttribute('brightcove_id', 1324538142001);
        $personMovie->setAttribute('product_title', "Camping 2");
        $personMovie->setAttribute('age_limit', '0');
        $personMovie->setAttribute('description', "Jean-Pierre Savelli, 45 ans,...");
        $personMovie->setAttribute('movie_duration', 'test user');
        $personMovie->setAttribute('imdb_id', 'test user');
        $personMovie->setAttribute('movie_duration', 0);
        $personMovie->setAttribute('premiere', "tt1503096");
        $personMovie->setAttribute('production_year', 2010);
        $personMovie->setAttribute('search_engine', 'bande+annonce+Camping+2+sortie+cinema');
        $personMovie->setAttribute('official_website', '');
        $personMovie->setAttribute('person_type_id', 1);
        $personMovie->setAttribute('name_person_type', 'actor');

        return new Collection([$personMovie]);
    }

    private function getPictureMovie(): Collection
    {
        $picture = new PictureMovie();
        $picture->setAttribute('picture_id', 36395);
        $picture->setAttribute('url', 'http://fr.image-1.filmtrailer.com/36395.jpg');
        $picture->setAttribute('width', 560);
        $picture->setAttribute('height', 800);
        $picture->setAttribute('mime_type', 'image/jpeg');
        $picture->setAttribute('picture_type_picture_type_id', 3);

        return new Collection([$picture]);
    }
}

