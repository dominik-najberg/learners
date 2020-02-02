<?php


namespace App\Infrastructure\Controller;


use App\Application\LuckyNumberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    public function index(LuckyNumberService $luckyNumberService)
    {
        $lndto = $luckyNumberService->getLuckyNumberDTO();

        return $this->render('lucky/number.html.twig', [
            'number' => $lndto->getNumber(),
        ]);
    }
}