<?php

declare(strict_types=1);

namespace App\OpenSkos\Set\Controller;

use App\OpenSkos\InternalResourceId;
use App\OpenSkos\Set\SetRepository;
use App\Ontology\OpenSkos;
use App\OpenSkos\ApiRequest;
use App\Rdf\Iri;
use App\Rest\ListResponse;
use App\Rest\ScalarResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @IgnoreAnnotation("OA\Get")
 * @IgnoreAnnotation("OA\Response")
 * @IgnoreAnnotation("OA\Parameter")
 * @IgnoreAnnotation("OA\Schema")
 */
final class Set
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
     * @Route(path="/sets", methods={"GET"})
     *
     * @param ApiRequest    $apiRequest
     * @param SetRepository $repository
     *
     * @return ListResponse
     *
     * @OA\Get(
     *     path="/sets",
     *     tags={"Sets"},
     *     description="Retrieve a list of sets",
     *     @OA\Response(response="200", description="Succesfull retrieval of a list of sets")
     * )
     */
    public function sets(ApiRequest $apiRequest, SetRepository $repository): ListResponse
    {
        $sets = $repository->all($apiRequest->getOffset(), $apiRequest->getLimit());

        return new ListResponse(
            $sets,
            count($sets),
            $apiRequest->getOffset(),
            $apiRequest->getFormat()
        );
    }

    /**
     * @Route(path="/set/{id}", methods={"GET"})
     *
     * @param InternalResourceId $id
     * @param ApiRequest         $apiRequest
     * @param SetRepository      $repository
     *
     * @return ScalarResponse
     *
     * @OA\Get(
     *     path="/set/{id}",
     *     tags={"Sets"},
     *     description="Retrieve a specific set",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of set to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="UUID",
     *             minLength=36,
     *             maxLength=36
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succesfull retrieval of an set"),
     *     @OA\Response(response=404, description="Set not found")
     * )
     */
    public function set(
        InternalResourceId $id,
        ApiRequest $apiRequest,
        SetRepository $repository
    ): ScalarResponse {
        $set = $repository->findOneBy(
            new Iri(OpenSkos::CODE),
            $id
        );

        if (null === $set) {
            throw new NotFoundHttpException("The set $id could not be retreived.");
        }

        return new ScalarResponse($set, $apiRequest->getFormat());
    }
}
