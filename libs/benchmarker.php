<?php

    /**
     * Allows to benchmark a part of code, with different units/precision
     */
    class benchmarker
    {
        private
            $start,
            $end,
            $unit,
            $textUnit,
            $digits,
            $totalTime,
            $tabresults;

        public function __construct()
        {
            $this->reset();
        }

        /**
         * Changes the unit currently in use
         * @param string $unit
         */
        public function setUnit($unit = 'ms')
        {
            switch ($unit)
            {
                case 'us':
                case 'µs':
                    $this->unit = 1e6;
                    $this->textUnit = 'µs';
                    break;
                case 's':
                    $this->unit = 1;
                    $this->textUnit = 's';
                    break;
                default:
                    $this->unit = 1e3;
                    $this->textUnit = 'ms';
            }
        }

        /**
         * @return float
         */
        public function getBenchmarkTime()
        {
            return ($this->end * $this->unit) - ($this->start * $this->unit);
        }

        /**
         * @static
         * @param string $text
         * @param bool $returnOnly
         * @return string|void
         */
        static public function out($text, $returnOnly = false)
        {
            $res = $text.(PHP_SAPI == 'cli' ? "\n" : "<br />");
            if (!$returnOnly) echo $res;
            return $res;
        }

        public function setPrecision($digits = 3)
        {
            $this->digits = $digits;
        }

        /**
         * Starts the benchmarking
         */
        public function start()
        {
            $this->start = self::getmicrotime();
        }

        /**
         * Stops the benchmarking
         */
        public function end()
        {
            $this->end = self::getmicrotime();
        }

        /**
         * Resets internal counters
         */
        public function reset()
        {
            $this->end = $this->start = $this->totalTime = 0;
            $this->unit = 1e3;
            $this->textUnit = 'ms';
            $this->digits = 3;
            $this->tabresults = array();
        }

        /**
         * Outputs time elapsed between start() and end() calls with supplied $label
         * @param $label
         */
        public function output($label)
        {
            if (!isset($this->unit)) $this->setUnit();
            $text = $this->textUnit;
            $res = sprintf("%.{$this->digits}f", $this->getBenchmarkTime());

            self::out("<p class='secondary label'> $label : $res $text.</p>");
            $this->totalTime += (double)$res;
        }

        /**
         * Outputs time elapsed between start() and end() calls with supplied $label
         * @param $label
         */
        public function outputTab($label)
        {
            if (!isset($this->unit)) $this->setUnit();
            $text = $this->textUnit;
            $res = sprintf("%.{$this->digits}f", $this->getBenchmarkTime());

            $this->tabresults[$label] = $res . $text;
            $this->totalTime += (double)$res;
        }

        /**
         * Outputs total time elapsed between successive calls of start() end() and output()
         */
        public function outputTotalTime()
        {
            self::out("<p clas='secondary label'>Temps total : ".$this->totalTime." ".$this->textUnit." used</p>");
        }

        /**
         * Outputs total time elapsed between successive calls of start() end() and output()
         */
        public function outputTotalTimeTab()
        {
            $this->tabresults["total"] = $this->totalTime." ".$this->textUnit." used";
        }

        /**
         * Private helper to get current microtime
         * @return float
         */
        static private function getmicrotime()
        {
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
        }

        /**
         * Adds the total time of the other benchmarker to this current one
         * @param benchmarker $otherBenchmarker
         */
        public function addTotalTime(benchmarker $otherBenchmarker)
        {
            $this->totalTime += $otherBenchmarker->totalTime;
        }

        public function displayTotalBenchmark()
        {
            foreach ($this->tabresults as $key => $value)
            {
                echo '<p class="secondary label">'.$key.' : ' . $value . '</p>';
            }
        }
    }
