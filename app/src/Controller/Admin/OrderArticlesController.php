<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderArticlesController extends AbstractController
{
    #[Route(path: '/admin/order/{id}/articles', name: "admin_order_article")]
    public function index(): Response
    {

        return $this->render('admin/orderarticles.html.twig');
    }
}
