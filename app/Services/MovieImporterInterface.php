<?php


namespace App\Services;


interface MovieImporterInterface
{
    public function import(string $file): array;
}
