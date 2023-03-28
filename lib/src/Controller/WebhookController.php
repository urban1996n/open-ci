<?php

namespace App\Controller;

use App\AMQP\JobMessage;
use App\Http\JobMessenger;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    #[Route('/webhook', methods: ['POST'])]
    public function githubWebhook(Request $request, JobMessenger $jobMessenger): Response
    {
        $input = $request->request;

        $commit = $input->get('after');
        $branch = \explode('/', $request->get('ref'));

        $message = new JobMessage(\end($branch), $commit);
        $jobMessenger->send($message);

        return new Response();
    }
}
