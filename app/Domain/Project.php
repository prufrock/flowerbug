<?php namespace App\Domain;

use Aws\SimpleDb\SimpleDbClient;
use Illuminate\Support\Facades\Storage;

class Project
{
    private $attributes;

    private $guideGateway;

    private $simpleDb;

    public function __construct(SimpleDbClient $simpleDb, Guide $guideGateway)
    {
        $this->simpleDb = $simpleDb;
        $this->guideGateway = $guideGateway;
    }

    public function create($attributes = [])
    {
        $project = new self($this->simpleDb, $this->guideGateway);
        $project->setAttributes($attributes);

        return $project;
    }

    public function getTitle()
    {
        return $this->attributes['title'];
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getThumb()
    {
        return $this->attributes['thumb'];
    }

    public function getUrl()
    {
        return $this->attributes['url'];
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function getCost()
    {
        return $this->attributes['cost'];
    }

    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function find($ids = null)
    {
        $predicateClauses = collect($ids)->map(function ($id) {
            return "id = '$id'";
        });

        $predicate = $predicateClauses->implode(' or ');

        $result = $this->simpleDb->select([
            'SelectExpression' => 'select * from '.config('flowerbug.simpledb.projects_domain').' where '.$predicate,
            'ConsistentRead' => true,
        ]);

        return collect(collect($result['Items'])->map(function ($item) {
            $title = '';
            foreach ($item['Attributes'] as $attribute) {
                if ($attribute['Name'] == 'name') {
                    $title = $attribute['Value'];
                }
            }

            return $this->create(['id' => $item['Name'], 'title' => $title]);
        }));
    }

    public function getGuides($type = null)
    {
        if ($type) {
            return $this->guideGateway->find($this->getId())->filter(function ($guide) use ($type) {
                return $guide->getFileType() == $type;
            });
        } else {
            return $this->guideGateway->find($this->getId());
        }
    }

    public function all()
    {
        $result = $this->simpleDb->select([
            'SelectExpression' => 'select * from '.config('flowerbug.simpledb.projects_domain'),
            'ConsistentRead' => true,
        ]);

        return collect(collect($result['Items'])->map(function ($item) {
            $title = '';
            $project = [];
            foreach ($item['Attributes'] as $attribute) {
                if ($attribute['Name'] == 'name') {
                    $title = $attribute['Value'];
                }
                $project[$attribute['Name']] = $attribute['Value'];
            }

            return $this->create(array_merge(['id' => $item['Name'], 'title' => $title], $project));
        }));
    }

    private function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}