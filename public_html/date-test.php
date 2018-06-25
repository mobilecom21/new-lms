<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

class mk {

function marking_days_total($start_date, $marking_days_available, $recursive=false) {
 //public holidays snatched from http://www.calendarpedia.co.uk/bank-holidays/bank-holidays-2022.html

        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        $holidayDays = ['*-12-25','*-12-26','*-01-01',  //christmas, boxing day, new years day
                        '2018-05-07',   //early may bank holiday
                        '2018-05-28',   //spring bank holiday
                        '2018-08-27',   //august bank holiday
                        '2019-04-19',   //good friday
                        '2019-04-22',   //easter monday
                        '2019-05-06',   //early may bank holiday
                        '2019-05-27',   //spring bank holiday
                        '2019-08-26',   //august bank holiday,
                        '2020-04-10',   //good friday
                        '2020-04-13',   //easter monday
                        '2020-05-04',   //early may bank holiday
                        '2020-05-25',   //spring bank holiday
                        '2020-08-31',   //august bank holiday
                        '2020-12-28',   //substitue for boxing day
                        '2021-04-02',   //good friday
                        '2021-04-05',   //easter monday
                        '2021-05-03',   //early may bank holiday
                        '2021-05-31',   //spring bank holiday
                        '2021-08-30',   //august bank holiday
                        '2021-12-27',   //substitue for christmas day
                        '2021-12-28',   //substitue for boxing day
                        '2022-01-03',   //substitue for new year's day
                        '2022-04-15',   //good friday
                        '2022-04-18',   //easter monday
                        '2022-05-02',   //early may bank holiday
                        '2022-05-30',   //spring bank holiday
                        '2022-08-29',   //august bank holiday
                        '2022-12-27',   //substitue for christmas day
                        '2023-01-02',   //substitue for new year's day
                        '2023-04-07',   //good friday
                        '2023-04-10',   //easter monday
                        '2023-05-01',   //early may bank holiday
                        '2023-05-29',   //spring bank holiday
                        '2023-08-28'    //august bank holiday
                        ]; // variable and fixed holidays

        //start from the date supplied (will usually be the work submission date)
        $from = new \DateTime($start_date);
        //begin the end date from the start date and then add the number of marking days 
        $to = new \DateTime($start_date);
        $to->modify("+{$marking_days_available} days");

        //set the interval period to be one day
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);

        //setup our vars for use later
        $weekenddays = 0;
        $setdays = 0;
        $regulardays = 0;

        //IT0R4TE!
        foreach ($periods as $period) {

            ##if you want to test this function independently
            ## remove the #comments for output that makes sense

            echo $period->format("Y-m-d");

            
            if (!in_array($period->format('N'), $workingDays)) {
                //if the days are weekend days, add to the counter
                $weekenddays++;
                echo " weekend";
            }

            if (in_array($period->format('Y-m-d'), $holidayDays)) {
                 //if the days are fixed or recurring public holidays, add to the counter
                $setdays++;
                echo " named holiday";
            }

            if (in_array($period->format('*-m-d'), $holidayDays)) {
                //if the days are fixed or recurring public holidays, add to the counter
                $regulardays++;
                echo " regular holiday";
            }
            echo "<br />";
            //normal day
            $days++;
            
            
        }

        //ok, we have our number of days. now we need to work out whether it's in fact elapsed.
        $adddays = $days + $weekenddays + $setdays + $regulardays;
        $today = new \DateTime("now");
        $from->modify("+{$adddays} days");

        //check if the start date plus the interval adjusted for working days is less than today
        if($from < $today) {
            //get the actual difference and return the days
            //format %r is a minus sign is applicable, %a is days
            $interval = $today->diff($from);
            return $interval->format("%r%a days");
        }

        
        //if we've not added enough working days because of awkward stuff like weekends and holidays, let's chuck them back into the bag and tot them up
        if(($weekenddays + $setdays + $regulardays) > 0) {
            $period->modify("+1 days");

            $days += $this->marking_days_left($period->format("Y-m-d"), ($weekenddays + $setdays + $regulardays), true)['days_to_mark'];
        }

        //we're all good. hot diggidy. send some days
        //add one because we ignore the submission date itself
        // (but not if we're running this function recursively to catch up on funny dates)
        if(!$recursive) {
        	echo "SUBMISSION DAY";
        	$calc_end = true;
        }

        $days_to_mark = $recursive ? $days : ++$days;

        return [
        	'start_date' => $start_date,
        	'days_to_mark' => $days_to_mark
        ];

        

    }

    public function marking_days_left($days_to_mark, $start_date) {
    		$start = new \DateTime("now");
	       	$end = new \DateTime("{$start_date}");
	       	$end->modify("+{$days_to_mark} days");
			$interval = $start->diff($end)->days;
			$days_remaining = $interval;
			$i = (int) $days_remaining;
			return $days_remaining;
    }

}
$from = '2018-05-05';
$k = new mk; 
$o = $k->marking_days_total($from, 10);

$foo = $k->marking_days_left($o['days_to_mark'], $o['start_date']);

echo "<br /><br>";
var_dump( $o, $foo  );

