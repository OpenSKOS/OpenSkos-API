<?php

declare(strict_types=1);

namespace App\OpenSkos\ConceptScheme\Controller;

use App\Ontology\OpenSkos;
use App\OpenSkos\ConceptScheme\ConceptSchemeRepository;
use App\OpenSkos\ApiRequest;
use App\OpenSkos\InternalResourceId;
use App\Rdf\Iri;
use App\Rest\ListResponse;
use App\Rest\ScalarResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @IgnoreAnnotation("OA\Get")
 * @IgnoreAnnotation("OA\Response")
 * @IgnoreAnnotation("OA\Parameter")
 * @IgnoreAnnotation("OA\Schema")
 */
final class ConceptScheme
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/conceptschemes", methods={"GET"})
     *
     * @param ApiRequest              $apiRequest
     * @param ConceptSchemeRepository $repository
     *
     * @return ListResponse
     *
     * @OA\Get(
     *     path="/conceptschemes",
     *     tags={"Conceptscheme"},
     *     description="Retrieve a list of conceptschemes",
     *     @OA\Response(response="200", description="Succesfull retrieval of a list of conceptschemes")
     * )
     */
    public function conceptschemes(
        ApiRequest $apiRequest,
        ConceptSchemeRepository $repository
    ): ListResponse {
        $conceptschemes = $repository->all($apiRequest->getOffset(), $apiRequest->getLimit());

        return new ListResponse(
            $conceptschemes,
            count($conceptschemes),
            $apiRequest->getOffset(),
            $apiRequest->getFormat()
        );
    }

    /**
     * @Route(path="/conceptscheme/{id}", methods={"GET"})
     *
     * @param InternalResourceId      $id
     * @param ApiRequest              $apiRequest
     * @param ConceptSchemeRepository $repository
     *
     * @return ScalarResponse
     *
     * @OA\Get(
     *     path="/conceptscheme/{id}",
     *     tags={"Conceptscheme"},
     *     description="Retrieve a specific conceptscheme",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of conceptscheme to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="UUID",
     *             minLength=36,
     *             maxLength=36
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succesfull retrieval of a conceptscheme"),
     *     @OA\Response(response=404, description="Conceptscheme not found")
     * )
     */
    public function conceptscheme(
        InternalResourceId $id,
        ApiRequest $apiRequest,
        ConceptSchemeRepository $repository
    ): ScalarResponse {
        $conceptscheme = $repository->findOneBy(
            new Iri(OpenSkos::CODE),
            $id
        );

        if (null === $conceptscheme) {
            throw new NotFoundHttpException("The conceptscheme $id could not be retreived.");
        }

        return new ScalarResponse($conceptscheme, $apiRequest->getFormat());
    }
}
