<?php

declare(strict_types=1);

namespace App\OpenSkos\Institution\Controller;

use App\Ontology\OpenSkos;
use App\OpenSkos\Institution\InstitutionRepository;
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
final class Institution
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
     * @Route(path="/institutions", methods={"GET"})
     *
     * @param ApiRequest            $apiRequest
     * @param InstitutionRepository $repository
     *
     * @return ListResponse
     *
     * @OA\Get(
     *     path="/institutions",
     *     tags={"Institutions"},
     *     description="Retrieve a list of institutions",
     *     @OA\Response(response="200", description="Succesfull retrieval of a list of institutions")
     * )
     */
    public function institutions(
        ApiRequest $apiRequest,
        InstitutionRepository $repository
    ): ListResponse {
        $institutions = $repository->all($apiRequest->getOffset(), $apiRequest->getLimit());

        return new ListResponse(
            $institutions,
            count($institutions),
            $apiRequest->getOffset(),
            $apiRequest->getFormat()
        );
    }

    /**
     * @Route(path="/institution/{id}", methods={"GET"})
     *
     * @param InternalResourceId    $id
     * @param ApiRequest            $apiRequest
     * @param InstitutionRepository $repository
     *
     * @return ScalarResponse
     *
     * @OA\Get(
     *     path="/institution/{id}",
     *     tags={"Institutions"},
     *     description="Retrieve a specific institution",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of institution to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="UUID",
     *             minLength=36,
     *             maxLength=36
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succesfull retrieval of an institution"),
     *     @OA\Response(response=404, description="Institution not found")
     * )
     */
    public function institution(
        InternalResourceId $id,
        ApiRequest $apiRequest,
        InstitutionRepository $repository
    ): ScalarResponse {
        $institution = $repository->findOneBy(
            new Iri(OpenSkos::CODE),
            $id
        );

        if (null === $institution) {
            throw new NotFoundHttpException("The institution $id could not be retreived.");
        }

        return new ScalarResponse($institution, $apiRequest->getFormat());
    }
}
