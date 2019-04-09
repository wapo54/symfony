<?php
/**
 * Created by PhpStorm.
 * User: student
 * Date: 4/1/2019
 * Time: 2:46 PM
 */

namespace App\Controller;

use App\Repository\PictureRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class defaultController extends AbstractController
{
    /**
     * @Route ("/", name="homepage")
     * @param Environment $twig
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function homepageAction(
        Request $request,
        Environment $twig,
        PictureRepository $repository,
        PaginatorInterface $paginator
    ) {
        $color = 'purple';
        return new Response (
            $twig->render(
                'Default/homepage.html.twig',
        [
            'color' => $color,
            'itemList' => [1,2,58,8,7,654,24],
            'currentDate' => new \Datetime(),
            'pictures' => $repository->findPaginated(
            $request,
            $paginator
            )]
        ));

    }

    /**
     * @Route ("/terms", name="terms_of_services")
     * @return Response
     */
    public function termsOfServicesAction()
    {
        return new Response ('<!DOCTYPE> 
        <html> <body> This are the terms ...</html> </body>');
    }
}