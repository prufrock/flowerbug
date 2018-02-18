<?php 

namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class GuideTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(\App\Domain\Guide::class, resolve(\App\Domain\Guide::class));
    }

    public function testCreateWithNameAndUrlAndFileType()
    {
        $guideGateway = new \App\Domain\Guide;
        $guide = $guideGateway->create([
                'name' => 'example.doc',
                'url' => 'http://example.com/example.doc',
                'file_type' => 'doc',
            ]);

        $this->assertInstanceOf(\App\Domain\Guide::class, $guide);
        $this->assertEquals('example.doc', $guide->getName());
        $this->assertEquals('http://example.com/example.doc', $guide->getUrl());
        $this->assertEquals('doc', $guide->getFileType());
    }

    public function testFindOneProjectOneGuide()
    {
        $iterator = m::mock(\Aws\S3\Iterator\ListObjectsIterator::class);
        $this->mockArrayIterator($iterator, [
            ['Key' => 'technique201707/guides/', 'Size' => 0],
            ['Key' => 'technique201707/guides/technique201707.pdf', 'Size' => 10],
        ]);
        $s3 = m::mock(\Aws\S3\S3Client::class);
        $s3->shouldReceive('getIterator')->with('ListObjects', [
            'Bucket' => config('flowerbug.s3.projects_bucket'),
            'Prefix' => 'technique201707/guides',
        ])->andReturn($iterator)->once();
        $s3->shouldReceive('getObjectUrl')->with(config('flowerbug.s3.projects_bucket'), 'technique201707/guides/technique201707.pdf', config('flowerbug.s3.signed_url_expiration'))->once();
        $guide = new \App\Domain\Guide($s3);

        $guides = $guide->find('technique201707');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
        $this->assertEquals('pdf', $guides[0]->getFileType());
        $this->assertEquals('technique201707.pdf', $guides[0]->getName());
    }

    public function testFindOneProjectThreeGuides()
    {
        $iterator = m::mock(\Aws\S3\Iterator\ListObjectsIterator::class);
        $this->mockArrayIterator($iterator, [
            ['Key' => 'technique201707/guides/', 'Size' => 0],
            ['Key' => 'technique201707/guides/technique201707.pdf', 'Size' => 10],
            ['Key' => 'technique201707/guides/technique201707.doc', 'Size' => 10],
            ['Key' => 'technique201707/guides/technique201707.jpg', 'Size' => 10],
        ]);
        $s3 = m::mock(\Aws\S3\S3Client::class);
        $s3->shouldReceive('getIterator')->with('ListObjects', [
            'Bucket' => config('flowerbug.s3.projects_bucket'),
            'Prefix' => 'technique201707/guides',
        ])->andReturn($iterator)->once();
        $s3->shouldReceive('getObjectUrl')->with(config('flowerbug.s3.projects_bucket'), 'technique201707/guides/technique201707.pdf', config('flowerbug.s3.signed_url_expiration'))->once();
        $s3->shouldReceive('getObjectUrl')->with(config('flowerbug.s3.projects_bucket'), 'technique201707/guides/technique201707.doc', config('flowerbug.s3.signed_url_expiration'))->once();
        $s3->shouldReceive('getObjectUrl')->with(config('flowerbug.s3.projects_bucket'), 'technique201707/guides/technique201707.jpg', config('flowerbug.s3.signed_url_expiration'))->once();
        $guide = new \App\Domain\Guide($s3);

        $guides = $guide->find('technique201707');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
        $this->assertEquals('pdf', $guides[0]->getFileType());
        $this->assertEquals('technique201707.pdf', $guides[0]->getName());
        $this->assertEquals('doc', $guides[1]->getFileType());
        $this->assertEquals('technique201707.doc', $guides[1]->getName());
        $this->assertEquals('jpg', $guides[2]->getFileType());
        $this->assertEquals('technique201707.jpg', $guides[2]->getName());
    }
}
