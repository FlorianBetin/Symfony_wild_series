<?php

namespace App\Service;

use App\Entity\Program;


class ProgramDuration
{
    public function calculate(Program $program): string
    {
        $minutes = 0;
        foreach ($program->getSeasons() as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $minutes += $episode->getDuration();
            }
        }
        $days=0;
        if ($minutes > 1440) {
        $days = floor($minutes / 1440);
        }
        $hours = floor(($minutes - ($days * 1440)) / 60);
        $minutes -= (($days * 1440) + ($hours * 60));

        $times = "";
        if($days !== 0) {
            $times = $days . " jours ";
        }
        $times .= $hours . ' heures et ' . $minutes . ' minutes';
        return $times;    
    }
}