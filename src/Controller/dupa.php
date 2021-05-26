<?php


namespace App\Controller;




use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class dupa extends AbstractController
{
    /**
     * @Route("/dupa")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('registration.html.twig', [
            'number' => $number,
        ]);
    }
}