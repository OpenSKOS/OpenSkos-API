<?php

declare(strict_types=1);

namespace App\Institution\Controller;

use App\Institution\InstitutionRepository;
use App\Rest\ListResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class Institution
{
    /**
     * @Route(path="/institutions", methods={"GET"})
     *
     * @param InstitutionRepository $repository
     *
     * @return JsonResponse
     */
    public function institutions(InstitutionRepository $repository): JsonResponse
    {
        $institutions = $repository->all();
        $properties = [];
        foreach ($institutions as $institution) {
            $properties[] = $institution->properties();
        }

        $list = new ListResponse($properties, 0, count($properties));

        return new JsonResponse($list->toArray());
    }
}
