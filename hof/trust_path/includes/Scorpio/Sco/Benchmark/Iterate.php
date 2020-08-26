<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

class Sco_Benchmark_Iterate extends Benchmark_Iterate
{

	/**
     * Benchmarks a function or method.
     *
     * @access public
     * @return void
     */
    function run()
    {
        $arguments     = func_get_args();
        $iterations    = array_shift($arguments);
        $function_name = array_shift($arguments);

        if (is_string($function_name) && strstr($function_name, '::')) {
            $function_name = explode('::', $function_name);
            $objectmethod  = $function_name[1];
        }

        if (is_string($function_name) && strstr($function_name, '->')) {
            list($objectname, $objectmethod) = explode('->', $function_name);

            $object = $GLOBALS[$objectname];

            is_callable(array($object, $objectmethod));

            for ($i = 1; $i <= $iterations; $i++) {
                $this->setMarker('start_' . $i);
                call_user_func_array(array($object, $objectmethod), $arguments);
                $this->setMarker('end_' . $i);
            }

            return(0);
        }

        is_callable($function_name);

        for ($i = 1; $i <= $iterations; $i++) {
            $this->setMarker('start_' . $i);
            call_user_func_array($function_name, $arguments);
            $this->setMarker('end_' . $i);
        }
    }

    function get($simple_output = false)
    {
        $result = array();
        $total  = 0;

        $iterations = count($this->markers)/2;

        for ($i = 1; $i <= $iterations; $i++) {
            $time = $this->timeElapsed('start_'.$i, 'end_'.$i);

            if (0 && extension_loaded('bcmath')) {
                $total = bcadd($total, $time, 6);
            } else {
                $total = $total + $time;
            }

            if (!$simple_output) {
                $result[$i] = sprintf('%0.8f', $time);
            }
        }

        if (0 && extension_loaded('bcmath')) {
            $result['mean'] = bcdiv($total, $iterations, 6);
        } else {
            $result['mean'] = sprintf('%0.8f', $total / $iterations);
        }

        $result['iterations'] = $iterations;

        return $result;
    }

    /**
     * Wrapper for microtime().
     *
     * @return float
     * @access private
     * @since  1.3.0
     */
    function _getMicrotime()
    {
        return microtime(true);
    }

    function timeElapsed($start = 'Start', $end = 'Stop')
    {
        if ($end == 'Stop' && !isset($this->markers['Stop'])) {
            $this->markers['Stop'] = $this->_getMicrotime();
        }
        $end   = isset($this->markers[$end]) ? $this->markers[$end] : 0;
        $start = isset($this->markers[$start]) ? $this->markers[$start] : 0;

        if (0 && extension_loaded('bcmath')) {
            return bcsub($end, $start, 8);
        } else {
            return $end - $start;
        }
    }

}
