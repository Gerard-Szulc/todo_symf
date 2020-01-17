<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/items")
 */
class ItemController extends AbstractController
{

    /**
     * @Route("/", name="item_index", methods={"GET"})
     * @param ItemRepository $itemRepository
     * @param SerializerService $serializerService
     * @return Response
     */
    public function getItems(ItemRepository $itemRepository, SerializerService $serializerService): Response
    {

        $items = $itemRepository->findAll();
        usort($items, static function ($object1, $object2) {
            return $object1->getPosition() > $object2->getPosition();
        });

        return new Response($serializerService->serialize($items));

    }

    /**
     * @Route("/new", name="item_new", methods={"POST"})
     * @param Request $request
     * @param SerializerService $serializerService
     * @return Response
     * @throws \HttpResponseException
     */
    public function new(Request $request, SerializerService $serializerService): Response
    {
        $data = json_decode($request->getContent(), true);
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $newData = $form->submit($data)->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newData);
            $entityManager->flush();

            return new Response($serializerService->serialize($item));
        }

        return new Response('Could not add an item.',Response::HTTP_BAD_REQUEST);


    }



    /**
     * @Route("/{id}", name="item_show", methods={"GET"})
     * @param Item $item
     * @return Response
     */
    public function show(Item $item): Response
    {
        return $this->json([
            'item' => $item
        ]);
    }

    /**
     * @Route("/reorder", name="item_reorder", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function reorder(Request $request, EntityManagerInterface $em): Response
    {
        $items = json_decode($request->getContent())->items;
        $existingItems = $this->getDoctrine()->getRepository(Item::class)->findAll();

        foreach ($items as $item) {
            foreach ($existingItems as $existingItem) {
                if ($existingItem->getId() === $item->id) {
                    $data = $existingItem->setPosition($item->position);
                    $em->persist($data);
                }
            }
        }

        try {
            $this->getDoctrine()->getManager()->flush();
            return $this->json(['success' => 'data submited succesfully']);

        } catch (Exception $exception) {
            return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="item_delete", methods={"DELETE"})
     * @param Request $request
     * @param Item $item
     * @return Response
     */
    public function delete(Request $request, Item $item, SerializerService $serializerService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($item);
        $entityManager->flush();

        return new Response($serializerService->serialize('Success.item_removed'));
    }
}
