<?php

namespace App\Controller;

use App\AMQP\JobMessage;
use App\Http\JobMessenger;
use App\Request\JsonToInputBagDecorator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    public function __construct(private readonly JsonToInputBagDecorator $decorator)
    {
    }

    #[Route('/webhook', methods: ['POST'])]
    public function githubWebhook(Request $request, JobMessenger $jobMessenger): Response
    {
        //Todo rewrite to validator service
        $errors = [];
        $input  = $request->request;

        if ($request->request->has('payload')) {
            $input = $this->decorator->decorate($request->get('payload'));
        }

        $commit = $input->get('after');
        $branch = $input->get('ref');
        $branch = \str_contains($branch, '/') ? \explode('/', $branch) : [$branch];
        $branch = \end($branch);

        if (!$branch) {
            $errors[] = 'Missing `ref` parameter in request';
        }

        if (!$commit) {
            $errors[] = ('Missing `after` parameter in request');
        }

        if ($errors) {
            return new Response(\implode(', ', $errors), Response::HTTP_BAD_REQUEST);
        }

        $message = new JobMessage($branch, $commit);
        $jobMessenger->send($message);

        return new Response();
    }
}
