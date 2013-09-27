<?php

/*
 * This file is part of the MVS Core Bundle.
 *
 * (c) Minerals Value Service GmbH <info@mvs-corp.com>
 *
 * All rights reserved!
 */

namespace Hautelook\GearmanBundle\Model;

use GearmanTask;

/**
 * Class GearmanTaskStatus
 *
 * @package Hautelook\GearmanBundle\Model
 * @author Anton Stöckl <a.stoeckl@mvs-corp.com>
 */
class GearmanTaskStatus implements \Iterator
{
    /** @var array  */
    protected $results = array();

    /** @var bool */
    protected $hasErrors = false;

    /** @var int */
    protected $totalJobs = 0;

    /** @var int */
    protected $succJobs = 0;

    /** @var int */
    protected $failedJobs = 0;

    /** @var int */
    protected $pos = 0;

    /** @var array */
    protected $keys = array();

    /**
     * Push one result item onto the results array.
     *
     * @param GearmanTask $task
     * @param bool $hasError
     * @throws \InvalidArgumentException
     */
    public function pushResult(GearmanTask $task, $hasError = false)
    {
        if (! is_bool($hasError)) {
            throw new \InvalidArgumentException("Parameter [success] must be boolean");
        }

        $this->totalJobs++;

        if ($hasError === true && $this->hasErrors === false) {
            $this->hasErrors = true;
            $this->failedJobs++;
        } else {
            $this->succJobs++;
        }

        $handle = $task->jobHandle();
        $this->results[$handle]['hasError'] = $hasError;
        $this->results[$handle]['task'] = $task;

        $this->rewind();
        $this->keys = array_keys($this->results);
    }

    /**
     * Getter for the hasErrors property, return whether all tasks in this instance were successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return ($this->hasErrors === true) ? false : true;
    }

    /**
     * Getter for the totalJobs property.
     *
     * @return int
     */
    public function getTotalJobs()
    {
        return $this->totalJobs;
    }

    /**
     * Getter for the succJobs property.
     *
     * @return int
     */
    public function getSuccessfulJobs()
    {
        return $this->succJobs;
    }

    /**
     * Getter for the failedJobs property.
     *
     * @return int
     */
    public function getFailedJobs()
    {
        return $this->failedJobs;
    }

    /**
     * Implements @see \Iterator::key()
     */
    public function key()
    {
        return $this->pos;
    }

    /**
     * Implements @see \Iterator::valid()
     */
    public function valid()
    {
        return array_key_exists($this->pos, $this->keys);
    }

    /**
     * Implements @see \Iterator::current()
     */
    public function current()
    {
        $key = $this->keys[$this->pos];

        return $this->results[$key];
    }

    /**
     * Implements @see \Iterator::next()
     */
    public function next()
    {
        $this->pos++;
    }

    /**
     * Implements @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->pos = 0;
    }
}
