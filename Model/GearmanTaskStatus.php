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
    protected $pos = 0;

    /** @var array */
    protected $keys = array();

    /**
     * Push one result item onto the results array.
     *
     * @param GearmanTask $task
     * @param bool $success
     * @throws \InvalidArgumentException
     */
    public function pushResult(GearmanTask $task, $success)
    {
        if (! is_bool($success)) {
            throw new \InvalidArgumentException("Parameter [success] must be boolean");
        }

        if ($success === false && $this->hasErrors === false) {
            $this->hasErrors = true;
        }

        $handle = $task->jobHandle();
        $this->results[$handle]['success'] = $success;
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
        return $this->hasErrors;
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
        return $this->results[$this->pos];
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
