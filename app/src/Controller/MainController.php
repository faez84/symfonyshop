<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpKernel\Attribute\Cache;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'app_main')]
 
    public function index(): Response
    {
        // get the login error if there is one
        $response = $this->render('index.html.twig', [
            'mesg' => 'HI'
        ]) ;

        $response->setSharedMaxAge(30);
        $response->mustRevalidate();
        return $response;
    }

    #[Route("/cp/userHome", 'app_user_home')]
    public function homeLogin(): Response
    {
        return $this->render('indexUser.html.twig');
    }
}
