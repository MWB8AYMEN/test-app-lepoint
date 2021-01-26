<?php


namespace App\Services;


use App\MoviePerson;
use App\Person;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

class JsonMovieImporter implements MovieImporterInterface
{
    /**
     * @param string $file
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function import(string $file): array
    {
        if(Storage::disk('local')->exists('data\/' . $file)){
            $content = Storage::disk('local')->get('data\/' . $file);

            if (is_string($content)) {
                $objects = $this->convertJsonToArray($content);
                dump($objects[0]);exit;
            }
        }
    }

    /**
     * @param string $content
     * @return array|array[]
     */
    public function convertJsonToArray(string $content): array
    {
        $objects = json_decode($content, true, 512);

        if (method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        return $this->wrap((array)$objects);
    }

    /**
     * @param array $objects
     * @return array|array[]
     */
    protected function wrap(array $objects): array
    {
        return (empty($objects) || is_array(reset($objects))) ? $objects : [$objects];
    }
}
