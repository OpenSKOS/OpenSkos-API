<?php

declare(strict_types=1);

namespace App\Rest\ArgumentResolver;

use App\OpenSkos\ApiRequest;
use App\OpenSkos\Exception\InvalidApiRequest;
use App\Rdf\Format\RdfFormat;
use App\Rdf\Format\RdfFormatFactory;
use App\Rdf\Format\UnknownFormatException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ApiRequestResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RdfFormatFactory
     */
    private $formatFactory;

    public function __construct(
        RdfFormatFactory $formatFactory
    ) {
        $this->formatFactory = $formatFactory;
    }

    /**
     * @param string|null $formatName
     * @param array|null  $headers
     *
     * @return RdfFormat|null
     *
     * @throws InvalidApiRequest
     */
    private function resolveFormat(?string $formatName, $headers = null): ?RdfFormat
    {
        if (null === $formatName) {
            // Attempt using the accept header
            if (isset($headers['accept'])) {
                // Build accept list
                $accepts = [];
                foreach ($headers['accept'] as $list) {
                    $list = str_getcsv($list);
                    foreach ($list as $entry) {
                        if (false !== strpos($entry, ';')) {
                            $entry = explode(';', $entry)[0];
                        }
                        array_push($accepts, $entry);
                    }
                }

                // Attempt using the mimetype
                foreach ($accepts as $mime) {
                    $format = $this->formatFactory->createFromMime($mime);
                    if (!is_null($format)) {
                        return $format;
                    }
                }
            }

            return null;
        }

        try {
            return $this->formatFactory->createFromName($formatName);
        } catch (UnknownFormatException $e) {
            throw new InvalidApiRequest('Invalid Format', 0, $e);
        }
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return ApiRequest::class === $argument->getType();
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     *
     * @throws InvalidApiRequest
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $allParameters = $request->query->all();

        $institutions = $request->query->get('institutions', '');
        $institutions = preg_split('/\s*,\s*/', $institutions, -1, PREG_SPLIT_NO_EMPTY);

        $sets = $request->query->get('sets', '');
        $sets = preg_split('/\s*,\s*/', $sets, -1, PREG_SPLIT_NO_EMPTY);

        //B.Hillier. The specs from Menzo ask for a 'foreign uri' as a parameter. I have no idea how this is stored
        // at Meertens. For now it just searches on the same field as the 'native' uri
        $foreignUri = $request->query->get('uri', null);

        $searchProfile = $request->query->getInt('searchProfile');

        $formatName = $request->query->get('format');
        if (is_null($formatName)) {
            $formatName = $request->attributes->get('format');
            if (is_string($formatName) && (!strlen($formatName))) {
                $formatName = null;
            }
        }

        yield new ApiRequest(
            $allParameters,
            $this->resolveFormat($formatName, $request->headers->all()),
            $request->query->getInt('level', 1),
            $request->query->getInt('limit', 100),
            $request->query->getInt('offset', 0),
            $institutions,
            $sets,
            $searchProfile,
            $foreignUri
        );
    }
}
