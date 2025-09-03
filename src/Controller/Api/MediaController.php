<?php

namespace App\Controller\Api;

use App\Entity\Enum\MediaAssetType;
use App\Entity\MediaAsset;
use App\Entity\User;
use App\Service\MinioClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/media')]
class MediaController extends AbstractController
{
    public function __construct(
        private readonly MinioClient $minioClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/upload', name: 'api_media_upload', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function upload(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        $constraints = new Assert\File([
            'maxSize' => '10M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'video/mp4',
                'video/quicktime',
                'application/pdf',
            ],
            'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, GIF), video (MP4, MOV), or PDF file.',
        ]);

        $violations = $this->validator->validate($file, $constraints);
        if (count($violations) > 0) {
            $errors = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
            return new JsonResponse(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $uploadResult = $this->minioClient->upload($file);
        } catch (\Exception $e) {
            $this->logger->error('MinIO Upload Failed: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['error' => 'Could not upload the file.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /** @var User $user */
        $user = $this->getUser();
        $mediaAsset = new MediaAsset();

        $mimeType = $file->getMimeType();
        $type = match (true) {
            str_starts_with($mimeType, 'image/') => MediaAssetType::IMAGE,
            str_starts_with($mimeType, 'video/') => MediaAssetType::VIDEO,
            default => MediaAssetType::PDF,
        };

        // The setters are assumed to exist based on the previous request's entity generation
        $mediaAsset->setOwner($user);
        $mediaAsset->setType($type);
        $mediaAsset->setOriginalName($file->getClientOriginalName());
        $mediaAsset->setMime($mimeType);
        $mediaAsset->setSize($file->getSize());
        $mediaAsset->setStoragePath($uploadResult['storagePath']);
        $mediaAsset->setSha256($uploadResult['sha256']);
        $mediaAsset->setMeta(['disclaimer' => 'AI Support: for informational purposes — not a medical diagnosis.']);

        $this->entityManager->persist($mediaAsset);
        $this->entityManager->flush();

        $signedUrl = $this->minioClient->getPresignedUrl($mediaAsset->getStoragePath());

        return new JsonResponse([
            'id' => $mediaAsset->getId(),
            'type' => $mediaAsset->getType()->value,
            'originalName' => $mediaAsset->getOriginalName(),
            'signedUrl' => $signedUrl,
            'createdAt' => $mediaAsset->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ], Response::HTTP_CREATED);
    }
}
