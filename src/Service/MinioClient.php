<?php

namespace App\Service;

use Aws\S3\S3Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MinioClient
{
    public function __construct(
        private readonly S3Client $s3Client,
        private readonly string $bucketName
    ) {
    }

    /**
     * Uploads a file to the MinIO bucket and returns its path and hash.
     *
     * @param UploadedFile $file
     * @return array{'storagePath': string, 'sha256': string}
     * @throws \Exception
     */
    public function upload(UploadedFile $file): array
    {
        $uuid = Uuid::uuid4()->toString();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Sanitize the filename to prevent issues with object storage paths
        $safeFilename = preg_replace('/[^A-Za-z0-9-_.]/', '', $originalFilename);
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $storagePath = sprintf('media/%s/%s.%s', $uuid, $safeFilename, $extension);

        $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => $storagePath,
            'SourceFile' => $file->getRealPath(),
            'ContentType' => $file->getMimeType(),
        ]);

        return [
            'storagePath' => $storagePath,
            'sha256' => hash_file('sha256', $file->getRealPath()),
        ];
    }

    /**
     * Generates a temporary presigned URL to access a private object.
     *
     * @param string $storagePath The full key/path of the object in the bucket.
     * @param string $expires The expiration time string (e.g., '+10 minutes').
     * @return string The presigned URL.
     */
    public function getPresignedUrl(string $storagePath, string $expires = '+10 minutes'): string
    {
        $command = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucketName,
            'Key' => $storagePath,
        ]);

        $request = $this->s3Client->createPresignedRequest($command, $expires);

        return (string) $request->getUri();
    }
}
