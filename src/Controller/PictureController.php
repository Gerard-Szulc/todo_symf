<?php


namespace App\Controller;


use App\Entity\Item;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Managers\UploadFileManager;
use App\Repository\ItemRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pictures")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/{uuid}", name="get_picture", methods={"GET"})
     * @param Picture $picture
     * @param ItemRepository $itemRepository
     * @param SerializerService $serializerService
     * @return Response
     */
    public function getPicture(Picture $picture): Response
    {
        $file = $this->getParameter('pictures_directory').'/'.$picture->getImageName();

        return new BinaryFileResponse($file);
    }

    /**
     * @Route("/{id}/upload", name="post_picture", methods={"POST"})
     * @param Request $request
     * @param Item $item
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function uploadPicture(Request $request, Item $item, EntityManagerInterface $entityManager, SerializerService $serializerService, UploadFileManager $uploadFileManager): Response
    {
        $picture = new Picture();

        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);
        $data = $form->submit(['name' => $request->files->get('file')])->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $ulopadDestination = $this->getParameter('pictures_directory');

            $uploadFileManager->createFile( $picture, $item, $form, $data, $request, $ulopadDestination, $entityManager);


            return new Response($serializerService->serialize($item));
        }



        return $this->json([
//            'error' => 'Could not add an item.'
            'error' => $form->isValid(),
            'error2' => $form->getErrors()
        ]);
    }
}
