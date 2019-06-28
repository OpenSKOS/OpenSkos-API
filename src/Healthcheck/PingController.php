<?php

declare(strict_types=1);

namespace App\Healthcheck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IgnoreAnnotation("OA\Get")
 * @IgnoreAnnotation("OA\Response")
 */
final class PingController extends AbstractController
{
    /**
     * @Route(path="/ping", methods={"GET"})
     *
     * @OA\Get(
     *     path="/ping",
     *     tags={"Health check"},
     *     description="A health check function to see if the API is functioning",
     *     @OA\Response(response="200", description="Expected response: Hello OpenSkos world!"),
     *     @OA\Response(response="404", description="The API is not functioning!")
     * )
     */
    public function ping(): Response
    {
        return new Response('Hello OpenSkos world!');
    }
}
