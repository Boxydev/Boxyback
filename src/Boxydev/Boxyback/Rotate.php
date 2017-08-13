<?php

namespace Boxydev\Boxyback;

class Rotate
{
    private $now;
    private $dates = array();
    private $directory;

    public function __construct($directory, $frequency)
    {
        $this->now = new \DateTimeImmutable();
        $this->setDay($frequency['day'] ? $frequency['day'] : 7);
        $this->setWeek(@$frequency['week']);
        $this->setMonth(@$frequency['month']);
        $this->setYear(@$frequency['year']);
        $this->directory = $directory;
        $this->rotate();
    }

    public function setDay($nbDay)
    {
        for ($i = 0; $i < $nbDay; $i++) {
            $this->dates[] = $this->now->modify('-'.$i.' day')->format("Y-m-d");
        }
    }

    public function setWeek($nbWeek)
    {
        for ($i = 0; $i < $nbWeek; $i++) {
            $this->dates[] = $this->now->modify('last Sunday - '.($i+1).' week')->format("Y-m-d");
        }
    }

    public function setMonth($nbMonth)
    {
        for ($i = 0; $i < $nbMonth; $i++) {
            $month = $this->now->modify('- '.($i+1).' month')->format('M');
            $year = $this->now->modify('- '.($i+2).' month')->format('Y');
            $this->dates[] = $this->now->modify("first Sunday of ".$month." ".$year)->format("Y-m-d");
        }
    }

    public function setYear($nbYear)
    {
        for ($i = 0; $i < $nbYear; $i++) {
            $year = $this->now->modify('- '.($i+1).' year')->format('Y');
            $this->dates[] = $this->now->modify("first Sunday of Jan ".$year)->format("Y-m-d");
        }
    }

    public function strpos_arr($haystack, $needle)
    {
        if (!is_array($needle)) $needle = array($needle);
        foreach ($needle as $what) {
            if (($pos = strpos($haystack, $what)) !== false) return $pos;
        }

        return false;
    }

    public function rotate()
    {
        $scanned_directory = array_diff(scandir($this->directory), array('..', '.'));
        foreach ($scanned_directory as $file) {
            if ($this->strpos_arr($file, $this->dates) === false) {
                unlink($this->directory."/".$file);
            }
        }
    }

}
