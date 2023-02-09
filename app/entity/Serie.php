<?php

namespace App\Entity;

class Serie extends AbstractEntity
{
    const ONE_TO_MANY = [
        'directorId' => Director::class,
        'genreId' => Genre::class
    ];
    protected $id;
    protected $title;
    protected $description;
    protected $numberOfEpisodes;
    protected $episodeLength;
    protected $directorId;
    protected $genreId;
    protected $idYoutube;
    protected $picture;
    protected $publishedAt;
    protected $createdAt;
    protected $updatedAt;
}
