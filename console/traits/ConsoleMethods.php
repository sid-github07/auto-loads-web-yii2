<?php

namespace console\traits;

trait ConsoleMethods
{
    /**
     * @param \DateTime $start
     * @param \DateTime $elapsed
     * @return string
     */
    public function elapsedTimeString(\DateTime $start, \DateTime $elapsed)
    {
        $interval = $start->diff($elapsed);
        return $interval->h . ":" . $interval->i . ':' . $interval->s;
    }
}