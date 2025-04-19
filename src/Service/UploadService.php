<?php

namespace App\Service;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadService
{
  private S3Client $s3Client;
  private string $awsBucket;
  private string $awsKeyId;
  private string $awsSecretKey;

  public function __construct(S3Client $s3Client, string $awsBucket, string $awsKeyId, string $awsSecretKey)
  {
    $this->s3Client = new S3Client([
      'version' => 'latest',
      'region' => 'us-east-1',
      'credentials' => [
        'key' => $awsKeyId,
        'secret' => $awsSecretKey,
      ],
    ]);

    $this->awsBucket = $awsBucket;
  }

  public function uploadAudio(UploadedFile $file, $category): string
  {
    try {
      $key = $category . '/' . $file->getClientOriginalExtension();
      $result = $this->s3Client->putObject([
        'Bucket' => $this->awsBucket,
        'Key' => $key,
        'SourceFile' => $file->getPathname(),
        'ACL' => 'public-read',
      ]);
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $result['ObjectURL'];
  }
}
