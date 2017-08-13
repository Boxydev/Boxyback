<?php

namespace Boxydev\Boxyback;

class Rotate
{
    /**
     * @var \DateTimeImmutable
     * Now is immutable because we want to compare to base date
     */
    private $now;
    /**
     * @var string
     * Directory where we want to rotate files
     */
    private $directory;
    private $dates = array();

    public function __construct($directory)
    {
        $this->now = new \DateTimeImmutable();
        $this->directory = $directory;
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

    private function _strpos_arr($haystack, $needle)
    {
        if (!is_array($needle)) $needle = array($needle);
        foreach ($needle as $what) {
            if (false !== ($pos = strpos($haystack, $what))) return $pos;
        }

        return false;
    }

    public function run()
    {
        $scanned_directory = array_diff(scandir($this->directory), array('..', '.'));
        foreach ($scanned_directory as $file) {
            if (false === $this->_strpos_arr($file, $this->dates)) {
                unlink($this->directory.'/'.$file);
            }
        }
    }

}
