<?php


namespace App\Managers;


use App\Entity\Item;
use App\Entity\Picture;
use Doctrine\ORM\Id\UuidGenerator;

class UploadFileManager
{
    public function createFile (Picture $picture, Item $item, $form, $data, $request, $ulopadDestination, $entityManager): void
    {
        $originalName = $form->getExtraData()['name']->getClientOriginalName();
        $data->setItem($item);
        $generator = new UuidGenerator();
        $data->setUuid($generator->generate($entityManager, $data));
        $data->setName($originalName);
        $data->setImageName($picture->getUuid().str_replace(' ','_',$originalName));
        $data->setImageSize($request->files->get('file')->getSize());
        $data->getItem()->setFilePath($picture->getUuid().str_replace(' ','_',$originalName));

        $uploadedFile = $form->getExtraData()['name'];
        $uploadedFile->move($ulopadDestination, $data->getImageName());

        $entityManager->persist($data);
        $entityManager->flush();
    }
}
