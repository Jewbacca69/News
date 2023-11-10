<?php

namespace App\Controllers;

use App\NewsApi;
use Carbon\Carbon;

class NewsController extends BaseController
{
    private NewsApi $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new NewsApi();
    }

    public function index(): string
    {
        return $this->render('index.twig', ['headlinesCollection' => $this->api->getHeadlines()]);
    }

    public function setCountry(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['country'])) {
            $_SESSION['country'] = $_GET['country'];
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function search(): string
    {
        $question = $_GET["question"] ?? "";
        $from = $_GET["from"] ?? "";
        $to = $_GET["to"] ?? "";

        if (!empty($from)) {
            $carbonDate = Carbon::createFromFormat('m/d/Y', $from);
            $from = $carbonDate->format('Y-m-d');
        }

        if (!empty($to)) {
            $carbonDate = Carbon::createFromFormat('m/d/Y', $to);
            $to = $carbonDate->format('Y-m-d');
        }

        return $this->render('search.twig', ['headlinesCollection' => $this->api->searchNews($question, $from, $to)]);
    }
}