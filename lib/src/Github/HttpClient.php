<?php

namespace App\Github;

use App\Github\Request\RequestFactory;
use App\Github\Request\RequestType;
use App\Job\Job;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class HttpClient extends Client
{
    public function __construct(string $githubApiToken, private readonly RequestFactory $factory)
    {
        parent::__construct([
            'base_uri' => 'https://api.github.com/',
            'headers'  => [
                'Authorization' => 'token ' . $githubApiToken,
                'Accept'        => 'application/vnd.github.v3+json',
            ],
        ]);
    }

    public function createStatusCheck(Job $job): ResponseInterface
    {
        return $this->send($this->factory->create(RequestType::COMMIT_STATUS_UPDATE, $job));
    }
}
