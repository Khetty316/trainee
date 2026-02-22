<?php

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timeZone" GET parameter will force all ISO8601 date stings to a given timeZone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------

namespace frontend\models\common\fullcalendar;

require dirname(__FILE__) . '/utils.php';

class GetEvents {

    public function getLeaveCalendar2($dateStart, $dateEnd) {
        // Require our Event class and datetime utilities
        // Short-circuit if the client did not give us a date range.
        if (!isset($dateStart) || !isset($dateEnd)) {
            die("Please provide a date range.");
        }

        // Parse the start/end parameters.
        // These are assumed to be ISO8601 strings with no time nor timeZone, like "2013-12-29".
        // Since no timeZone will be present, they will parsed as UTC.
        $range_start = parseDateTime($dateStart);
        $range_end = parseDateTime($dateEnd);

        // Parse the timeZone parameter if it is present.
        $time_zone = null;
        if (isset($_GET['timeZone'])) {
            $time_zone = new DateTimeZone($_GET['timeZone']);
        }

        // Read and parse our events JSON file into an array of event data arrays.
//        $json = file_get_contents(dirname(__FILE__) . '/json/events.json');
        $leave = new \frontend\models\office\leave\VMasterLeave();
        $json = $leave->getLeaveForCalendar_Json();
        $input_arrays = json_decode($json, true);

        // Accumulate an output array of event data arrays.
        $output_arrays = array();
        foreach ($input_arrays as $array) {

            // Convert the input array into a useful Event object
            $event = new \Event($array, $time_zone);

            // If the event is in-bounds, add it to the output
            if ($event->isWithinDayRange($range_start, $range_end)) {
                $output_arrays[] = $event->toArray();
            }
        }

        // Send JSON to the client.
        return json_encode($output_arrays);
    }

    
    
    public function getCompleteCalendar(){
        $leave = $this->getLeaveCalendar();
        $holiday= $this->getHolidayCalendar();
        
        return json_encode(array_merge(json_decode($leave, true),json_decode($holiday, true)));
    }
    
    public function getLeaveCalendar() {

        // Read and parse our events JSON file into an array of event data arrays.
        $leave = new \frontend\models\office\leave\VMasterLeave();
        $json = $leave->getLeaveForCalendar_Json();
        return $this->processJson($json);
    }
    public function getHolidayCalendar() {

        // Read and parse our events JSON file into an array of event data arrays.
        $holidays = new \frontend\models\working\leavemgmt\LeaveHolidays();
        $json = $holidays->getHolidaysForCalendar_Json();
        return $this->processJson($json);
    }

    public function processJson($json) {
        $time_zone = null;
        if (isset($_GET['timeZone'])) {
            $time_zone = new DateTimeZone($_GET['timeZone']);
        }
        $input_arrays = json_decode($json, true);
        $output_arrays = array();
        foreach ($input_arrays as $array) {
            // Convert the input array into a useful Event object
            $event = new \Event($array, $time_zone);
            $output_arrays[] = $event->toArray();
        }
        return json_encode($output_arrays);
    }

}
