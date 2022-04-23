<?php

namespace App\Controller;

use App\AppName;
use App\Tool\ComplexityCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DataScienceController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/data-science", name="data_science")
     */
    public function index(Request $request, ChartBuilderInterface $chartBuilder): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $requests = $this->getRequests();

        if (!is_array($requests) || count($requests) == 0) {
            return $this->render('data-science/index.html.twig', [
                'controller_name' => AppName::FULL,
            ]);
        }

        $graphValues = array_column(array_values($requests),'complexity');

        $min = min($graphValues);
        $max = max($graphValues);

        $labels = range(0, count($graphValues)-1); //array_keys($requests);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'P-Faktor',
                    'backgroundColor' => 'rgb(120, 99, 255)',
                    'borderColor' => 'rgb(120, 99, 240)',
                    'data' => $graphValues,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'P-Faktor'
                    ],
                    'suggestedMin' => $min,
                    'suggestedMax' => $max,
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Request Nr.'
                    ],
                ],
            ],
        ]);

        return $this->render('data-science/index.html.twig', [
            'controller_name' => AppName::FULL,
            'chart' => $chart,
        ]);
    }

    protected function getRequests(): ?array
    {
        $data = [];
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $requests = $this->entityManager->getRepository('App:Request')->getLast24Hours();
        foreach ($requests as $request) {
            $complexityCalculator = new ComplexityCalculator();
            $complexity = $complexityCalculator->calculateRequestComplexity($request);
            $data[$request->getTimestamp()->format('H:i:s')] = [
                'complexity' => $complexity,
            ];
        }
        return $data;
    }

}
