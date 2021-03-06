<?php

namespace Consolidare\Record;

use Consolidare\Mergeable\Mergeable;
use Consolidare\MergeStrategy\MergeStrategy;
use Consolidare\Record\Exception\PropertyDoesNotExistException;
use Consolidare\Record\Exception\RecordException;
use Consolidare\Record\Records;

class Record implements Records
{
    private $properties = [];
    private $previousRecord;

    public function __construct(MergeStrategy $strategy, Records $previousRecord, Mergeable $mergeable)
    {
        $this->loadPreviousRecord($previousRecord);
        $this->merge($strategy, $previousRecord, $mergeable);
    }

    public function property($property)
    {
        if (!isset($this->properties[$property])) {
            throw new PropertyDoesNotExistException();
        }

        return $this->properties[$property];
    }

    public function retrieve()
    {
        return $this->properties;
    }

    public function revert()
    {
        return $this->previousRecord;
    }

    private function loadPreviousRecord(Records $previousRecord)
    {
        $this->previousRecord = $previousRecord;
        $this->properties = $previousRecord->retrieve();
    }

    private function merge(MergeStrategy $strategy, Records $previousRecord, Mergeable $mergeable)
    {
        foreach ($mergeable->retrieve() as $property => $value) {
            try {
                $this->properties[$property] = $strategy->merge(
                    $property,
                    $previousRecord->property($property),
                    $value
                );
            } catch (RecordException $e) {
                $this->properties[$property] = $value;
            }
        }
    }
}