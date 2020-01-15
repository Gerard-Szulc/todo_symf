<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemsListType;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);
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
     * @Route("/edit", name="item_edit", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $items = json_decode($request->getContent())->items;

        $form = $this->createForm(ItemsListType::class, $items);
        $data = $form->submit($items)->getData();
        if ($form->isValid()) {
            foreach($data as $item) {
                $this->getDoctrine()->getManager()->persist($item);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->json(['success'=> 'data submited succesfully']);
        }

        return $this->json([
            'error' => $form->getErrors()
        ]);
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
