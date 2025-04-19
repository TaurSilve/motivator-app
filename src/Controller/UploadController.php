<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UploadService;

class UploadController extends AbstractController
{
  private UploadService $uploadService;

  public function __construct(UploadService $uploadService)
  {
    $this->uploadService = $uploadService;
  }

  #[Route('/UploadFile', name: 'upload_file', methods: ['POST'])]
  public function uploadFile(Request $request): JsonResponse
  {
    $file = $request->files->get('audio');
    if (!$file instanceof UploadedFile) {
      return new JsonResponse(['error' => 'No file uploaded!'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $data = json_decode($request->getContent(), true);
    if (!isset($data['category']) || null === $data['category']) {
      return new JsonResponse(['error' => 'No category provided!'], JsonResponse::HTTP_BAD_REQUEST);
    }

    try {
      $uploadUrl = $this->uploadService->uploadAudio($file, $data['category']);
      return new JsonResponse(['url' => $uploadUrl], JsonResponse::HTTP_OK);
    } catch (\Exception $e) {
      return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
