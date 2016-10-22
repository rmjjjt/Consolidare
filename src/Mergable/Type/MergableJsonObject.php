<?php

namespace Consolidare\Mergable\Type;

use Consolidare\Mergable\Mergable;
use stdClass;

class MergableJsonObject implements Mergable
{
    private $data = [];

    public function __construct(stdClass $data)
    {
        foreach ($data as $property => $value) {
            $this->data[$property] = $value;
        }
    }

    public function retrieve()
    {
        return $this->data;
    }
}