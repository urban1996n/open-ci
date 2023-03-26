<?php

namespace App\Github;

use App\Common\Status;
use App\Github\Request\RequestFactory;
use App\Github\Request\RequestType;
use App\Job\Data\Config;
use App\Job\Job;
use App\Pipeline\Exception\PipelineException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
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

    public function initCommitStatus(Config $job): ResponseInterface
    {
        return $this->send($this->factory->create(RequestType::COMMIT_STATUS_INIT, $job));
    }

    public function updateCommitStatus(Config $job, Status $status): ResponseInterface
    {
        return $this->send(
            $this->factory->create(RequestType::COMMIT_STATUS_UPDATE, $job, ['status' => $status->value])
        );
    }

    public function markStatusFailure(Config $job, string $reason): ResponseInterface
    {
        return $this->send($this->factory->create(RequestType::COMMIT_STATUS_UPDATE, $job, ['description' => $reason]));
    }

    public function downloadZipArchive(Config $job): ResponseInterface
    {
        return $this->send($this->factory->create(RequestType::REPOSITORY_DOWNLOAD, $job));
    }
}
