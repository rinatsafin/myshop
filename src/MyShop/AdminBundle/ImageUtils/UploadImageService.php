<?php

namespace MyShop\AdminBundle\ImageUtils;


use Eventviva\ImageResize;
use MyShop\AdminBundle\DTO\UploadedImageResult;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImageService
{
    /**
     * @var CheckImg
    */
    private $checkImg;

    /**
     * @var ImageNameGenerator
    */
    private $imageNameGenerator;

    private $uploadImageRootDir;

    private $photoFileName;

    private $smallPhotoName;


    public function __construct(CheckImg $checkImg, ImageNameGenerator $imageNameGenerator)
    {
        $this->checkImg = $checkImg;
        $this->imageNameGenerator = $imageNameGenerator;
    }

    public function setUploadImageRootDir($imageRootDir)
    {
        $this->uploadImageRootDir = $imageRootDir;
    }

    /**
     * @return UploadedImageResult
    */
    public function uploadImage(UploadedFile $uploadedFile, $productId)
    {
        $imageNameGenerator = $this->imageNameGenerator;

        $photoFileName = $productId . $imageNameGenerator->generateName() . "." . $uploadedFile->getClientOriginalExtension();
        $photoDirPath = $this->uploadImageRootDir;

        try {
            $uploadedFile->move($photoDirPath, $photoFileName);
        }
        catch (\Exception $exception) {
            echo "Can not move file!";
            throw $exception;
        }

        $img = new ImageResize($photoDirPath . $photoFileName);
        $img->resizeToBestFit(250, 200);
        $smallPhotoName = "small_" . $photoFileName;
        $img->save($photoDirPath . $smallPhotoName);

        $result = new UploadedImageResult($smallPhotoName, $photoFileName);

        return $result;
    }
}