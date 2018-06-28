<?php

// Class Name: MultiGetChunk
//
// A MultiGetChunk represents a single range request within
// the context of a file download. Since its a basic value object
// all properties are public.


class MultiGetChunk
{
    public $start; // The starting chunk of this segment.
    public $end;  // The ending chunk of the segment.

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __toString()
    {
        return sprintf("bytes=%d-%d", $this->start, $this->end);
    }
}
