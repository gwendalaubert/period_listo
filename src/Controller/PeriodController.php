<?php

namespace App\Controller;

use App\Constants\Constants;
use App\Entity\Period;
use App\Exceptions\PeriodException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PeriodController extends AbstractController
{
    #[Route('/api/new-leave', name: 'newLeave', methods: 'POST')]
    public function newLeave(Request $request): JsonResponse
    {
        $start = $request->request->get('start');
        $end = $request->request->get('end');

        if (!preg_match(Constants::PATTERN_RFC3339, $start) ||
            !preg_match(Constants::PATTERN_RFC3339, $end)) {
            return new JsonResponse([
                'code' => Constants::CODE_REGEX_NOT_MATCH,
                'message' => "Start and end should match RFC3339 requirements"
            ]);
        }

        try {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);

            $periods = array();
            $this->createPeriods(Period::LEAVE_TYPE, $startDate, $endDate, $periods);

            return new JsonResponse([
                'code' => Constants::CODE_SUCCESS,
                'periods' => json_decode($this->serializePeriods($periods))
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => Constants::CODE_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param String $type
     * @param \DateTime $start
     * @param \DateTime $end
     * @param array $periods
     * @return array
     * @throws PeriodException
     */
    public function createPeriods(String $type, \DateTime $start, \DateTime $end, array &$periods): array
    {
        $lastDayOfMonth = (clone $start)->modify('last day of')->setTime(23, 59, 59);

        if ($lastDayOfMonth < $end) {
            $firstDayOfMonth = (clone $lastDayOfMonth)->modify('+1 day')->setTime(0, 0, 0);
            $periods[] = new Period($type, $start, $lastDayOfMonth);
            $this->createPeriods($type, $firstDayOfMonth, $end, $periods);
        } else {
            $periods[] = new Period($type, $start, $end);
        }

        return $periods;
    }

    /**
     * @param array $periods
     * @return string
     */
    public function serializePeriods(array $periods) : string
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize(
            $periods,
            JsonEncoder::FORMAT
        );
    }
}