<?php

namespace App\Libs;

class SnowflakeId
{
    private $workerId;
    private $sequence = 0;
    private $lastTimestamp = -1;
    
    public function __construct($workerId = 1)
    {
        $this->workerId = $workerId;
    }
    
    public function generate()
    {
        $timestamp = $this->timeGen();
        
        if ($timestamp == $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & 4095;
            if ($this->sequence == 0) {
                $timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            $this->sequence = 0;
        }
        
        $this->lastTimestamp = $timestamp;
        
        return (($timestamp - 1420041600000) << 22) |
               ($this->workerId << 12) |
               $this->sequence;
    }
    
    private function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }
        return $timestamp;
    }
    
    private function timeGen()
    {
        return (int)(microtime(true) * 1000);
    }
}