<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemsListType;
use App\Form\ItemType;
use App\Repository\ItemRepository;
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
     * @return Response
     */
    public function index(ItemRepository $itemRepository): Response
    {
        return $this->json([
            'items' => $itemRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="item_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(),true);
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $newData = $form->submit($data)->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newData);
            $entityManager->flush();

            return $this->json([
                'item' => $item
            ]);
        }

        return $this->json([
            'error' => 'Could not add an item.'
        ]);
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
     * @Route("/reorder", name="item_edit", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $items = json_decode($request->getContent())->items;
        $existingItems = $this->getDoctrine()->getRepository(Item::class)->findAll();

            foreach($items as $item) {
                foreach ($existingItems as $existingItem) {
                    if($existingItem->getId() === $item->id) {
                        $data = $existingItem->setPosition($item->position);
                        $em->persist($data);
                        }
                    }
                }

            try {
                $this->getDoctrine()->getManager()->flush();
                return $this->json(['success'=> 'data submited succesfully']);

            } catch (Exception $exception) {
                $response = $this->json([
                    'error' => $exception->getMessage(),
                ]);

                return new Response($response, Response::HTTP_BAD_REQUEST);
            }
    }

    /**
     * @Route("/{id}", name="item_delete", methods={"DELETE"})
     * @param Request $request
     * @param Item $item
     * @return Response
     */
    public function delete(Request $request, Item $item): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($item);
        $entityManager->flush();

        return $this->json([
            'item' => $item
        ]);
    }
}
