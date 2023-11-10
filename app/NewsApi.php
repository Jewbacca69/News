<?php

namespace App;

use App\Collections\NewsCollection;
use App\Models\News;
use Carbon\Carbon;
use stdClass;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class NewsApi
{
    private const EVERYTHING_URL = "https://newsapi.org/v2/everything?";
    private const HEADLINES_URL = "https://newsapi.org/v2/top-headlines?";
    private const DEFAULT_IMAGE = "https://theherotoys.com/wp-content/uploads/2020/11/no-image-available_1.png";
    private const DEFAULT_COUNTRY = "us";
    private string $apiKey;

    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
        $this->apiKey = $_ENV["API_KEY"];
    }

    public function getHeadlines(): NewsCollection
    {
        $country = $_SESSION["country"] ?? self::DEFAULT_COUNTRY;
        $data = $this->fetchData(self::HEADLINES_URL . "apiKey=" . $this->apiKey . "&country=" . $country);

        $headlines = new NewsCollection();

        foreach ($data->articles as $headline) {
            $headlines->addNews(new News(
                $headline->source->name ?? "Unknown",
                $headline->author ?? "Unknown",
                $headline->title ?? "Unknown",
                $headline->description ?? "Unknown",
                $headline->url ?? "Unknown",
                $headline->urlToImage ?? self::DEFAULT_IMAGE,
                Carbon::parse($headline->publishedAt) ?? "Unknown",
                $headline->content ?? "Unknown"
            ));
        }
        return $headlines;
    }

    public function searchNews(string $query, string $fromDate, string $toDate): NewsCollection
    {
        $data = $this->fetchData(self::EVERYTHING_URL . "q=$query&apiKey=" . $this->apiKey . "&from=$fromDate&to=$toDate&sortBy=relevancy");

        $articles = new NewsCollection();

        foreach ($data->articles as $article) {
            $articles->addNews(new News(
                $article->source->name ?? "Unknown",
                $article->author ?? "Unknown",
                $article->title ?? "Unknown",
                $article->description ?? "Unknown",
                $article->url ?? "Unknown",
                $article->urlToImage ?? self::DEFAULT_IMAGE,
                Carbon::parse($article->publishedAt) ?? "Unknown",
                $article->content ?? "Unknown"
            ));
        }
        return $articles;
    }

    public function fetchData(string $url): stdClass
    {
        $request = $this->client->request("GET", $url);
        return json_decode($request->getContent());
    }
}