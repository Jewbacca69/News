<?php

namespace App\Collections;


use App\Models\News;

class NewsCollection
{
    private array $news;

    public function __construct()
    {
        $this->news = [];
    }

    public function addNews(News $news): void
    {
        $this->news[] = $news;
    }

    public function getNews(): array
    {
        return $this->news;
    }

}