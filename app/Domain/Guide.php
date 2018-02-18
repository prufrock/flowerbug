<?php 

namespace App\Domain;

use Aws\S3\S3Client;

class Guide
{
    private $attributes;

    private $s3;

    public function __construct(S3Client $s3 = null)
    {
        $this->s3 = $s3;
    }

    public function create($attributes = [])
    {
        $guide = new self;
        $guide->setAttributes($attributes);

        return $guide;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getUrl()
    {
        return $this->attributes['url'];
    }

    public function getFileType()
    {
        return $this->attributes['file_type'];
    }

    public function find($id)
    {
        $iterator = $this->s3->getIterator('ListObjects', [
            'Bucket' => config('flowerbug.s3.projects_bucket'),
            'Prefix' => $id.'/guides',
        ]);

        $guides = [];
        foreach ($iterator as $object) {
            if ($object['Size'] > 0) {

                $guides[] = $this->create([
                    'name' => last(explode('/', $object['Key'])),
                    'url' => $this->s3->getObjectUrl(config('flowerbug.s3.projects_bucket'), $object['Key'], config('flowerbug.s3.signed_url_expiration')),
                    'file_type' => last(explode('.', last(explode('/', $object['Key'])))),
                ]);
            }
        }

        return collect($guides);
    }

    private function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
