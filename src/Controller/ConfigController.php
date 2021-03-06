<?php

namespace App\Controller;

use App\Entity\Config;
use App\Service\ConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConfigController extends AbstractController
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(
        NormalizerInterface $normalizer,
        ConfigService $configService
    ) {
        $this->normalizer = $normalizer;
        $this->configService = $configService;
    }

    public function getConfig(): Response
    {
        $config = $this->configService->getConfig();

        $normalizedConfig = $this->normalizer->normalize(
            $config,
            Config::class,
            ['groups' => ['config_out']]
        );

        return JsonResponse::create($normalizedConfig, JsonResponse::HTTP_OK);
    }

    public function saveConfig(Request $request): Response
    {
        $data = json_decode((string) $request->getContent(), true);
        $newConfig = null;

        if (!$data || !is_array($data) || !isset($data['supportedYears']) || !isset($data['supportedHolidays'])) {
            return JsonResponse::create(
                ['detail' => 'Expected config with supported years and supported holidays.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $newConfig = $this->configService->saveConfig($data['supportedYears'], $data['supportedHolidays']);
        } catch (\Exception $exception) {
            $this->getDoctrine()->resetManager();

            return JsonResponse::create(
                ['detail' => 'One of supported year or supported holiday is not valid.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $normalizedConfig = $this->normalizer->normalize(
            $newConfig,
            Config::class,
            ['groups' => ['config_out']]
        );

        return JsonResponse::create($normalizedConfig, JsonResponse::HTTP_OK);
    }
}
