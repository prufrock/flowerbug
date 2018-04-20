<?php

namespace Tests\Unit\Domain;

use Tests\TestCase;
use Mockery as m;

class ProjectTest extends TestCase
{
    public function testNew()
    {
        $this->assertInstanceOf(\App\Domain\Project::class, resolve(\App\Domain\Project::Class));
    }

    public function testCreateANewProjectWithTitle()
    {
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $guideGateway = m::mock(\App\Domain\Guide::class);
        $projectGateway = new \App\Domain\Project($simpleDbClient, $guideGateway);
        $project = $projectGateway->create(['title' => 'February 2017 Technique Class']);

        $this->assertEquals('February 2017 Technique Class', $project->getTitle());
    }

    public function testFindReturnsACollection()
    {
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [],
        ]);
        $guideGateway = m::mock(\App\Domain\Guide::class);
        $project = new \App\Domain\Project($simpleDbClient, $guideGateway);
        $found = $project->find(['technique201702']);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $found);
    }

    public function testFindReturnsACollectionOfProjects()
    {
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [
                [
                    'Name' => 'technique201702',
                    'Attributes' => [
                        ['Name' => 'name', 'Value' => 'February 2017 Technique Class'],
                    ],
                ],
            ],
        ]);
        $guideGateway = m::mock(\App\Domain\Guide::class);
        $project = new \App\Domain\Project($simpleDbClient, $guideGateway);
        $found = $project->find(['technique201702']);

        $this->assertInstanceOf(\App\Domain\Project::class, $found->first());
    }

    public function testGetGuides()
    {
        $guideGateway = m::mock(\App\Domain\Guide::class);
        $guideGateway->shouldReceive('find')->with('technique201702')->andReturn(collect([$guideGateway]));
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [
                [
                    'Name' => 'technique201702',
                    'Attributes' => [
                        ['Name' => 'name', 'Value' => 'February 2017 Technique Class'],
                    ],
                ],
            ],
        ]);
        $project = (new \App\Domain\Project($simpleDbClient, $guideGateway))->find('technique201702');
        $guides = $project->first()->getGuides();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
        $this->assertEquals(1, $guides->count());
        $this->assertInstanceOf(\App\Domain\Guide::class, $guides->first());
    }

    public function testGetGuideWithType()
    {
        $guideGateway = m::mock(\App\Domain\Guide::class);
        $guideGateway->shouldReceive('find')->with('technique201702')->andReturn(collect([$guideGateway]));
        $guideGateway->shouldReceive('getFileType')->andReturn('pdf')->once();
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [
                [
                    'Name' => 'technique201702',
                    'Attributes' => [
                        ['Name' => 'name', 'Value' => 'February 2017 Technique Class'],
                    ],
                ],
            ],
        ]);
        $project = (new \App\Domain\Project($simpleDbClient, $guideGateway))->find('technique201702');
        $guides = $project->first()->getGuides('pdf');

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $guides);
        $this->assertEquals(1, $guides->count());
        $this->assertInstanceOf(\App\Domain\Guide::class, $guides->first());
    }

    public function testGetAllProjectsReturnsACollection()
    {
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [
                [
                    'Name' => 'technique201702',
                    'Attributes' => [
                        ['Name' => 'name', 'Value' => 'February 2017 Technique Class'],
                    ],

                ],
            ],
        ]);
        $project = new \App\Domain\Project($simpleDbClient, resolve(\App\Domain\Guide::class));
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $project->all());
    }

    public function testGetAllProjectsReturnsACollectionWithProjectsInIt()
    {
        $simpleDbClient = m::mock(\Aws\SimpleDb\SimpleDbClient::class);
        $simpleDbClient->shouldReceive('select')->andReturn([
            'Items' => [
                [
                    'Name' => 'technique201702',
                    'Attributes' => [
                        ['Name' => 'name', 'Value' => 'February 2017 Technique Class'],
                    ],

                ],
            ],
        ]);
        $project = new \App\Domain\Project($simpleDbClient, resolve(\App\Domain\Guide::class));
        $this->assertInstanceOf(\App\Domain\Project::class, $project->all()->first());
    }
}