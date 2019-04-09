<?php


namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureFormType;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PictureController extends AbstractController
{
    /**
     * @Route("/add/picture", name="add_picture")
     */
    public function addPicture(
        Request $request,
        FormFactoryInterface $formFactory,
        Environment $twig
    ) {
        $picture = new Picture();
        $form = $formFactory ->create(
            PictureFormType::class,
            $picture,
            ['standalone' => true]
        );
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form->isValid()){
// TODO: make the process here
// $form=$picture->
        //resize the image
            //open the image
            /**@var UploadedFile $file */
            $file = $form->get('file')->getData();

            //resize the image
                 //find the proportion between the height and width
                //resize the biggest dimension to 1000px
                //resize the other dimension based on the ratio
            //rename the image
            $fileName = Uuid::uuid4()->toString() .'.'. $file->getExtension() ;
            //save the image
            $file->move($this->getParameter('upload_directory'), $fileName);
            $picture->setPath($fileName);
            $picture->setSharer($this->getUser());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($picture);
            $manager->flush();
            //set the picture path
         //create a thumbnail
            //open the image
            //resize the image
            //rename the image
            //save the image
         //get doctrine
         //get manager
         //persist the new picture
         //flush
         //redirect to homepage

        }
        return new Response(
            $twig->render(
                'Picture/addForm.html.twig',
                ['form' => $form->createView()]
            )
        );
    }
}