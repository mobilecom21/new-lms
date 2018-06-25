<?php

namespace EventLogger\Helper;


use EventLogger\Model;
use EventLogger\Model\EventLoggerTable;
use EventLogger\Model\EventLoggerTableGateway;



/*

.TODO - make DB table for holding event log
.TODO - make Model/* for new table
TODO - Hook EventLogger up to model and run test inserts
TODO - Add EventLogger 

*/
class EventLogger
{

	private $eventLoggerTable;
    
	public function __construct(EventLoggerTableGateway $table) {

		#Throw new \Exception("KLFAHA2");

		#EventLoggerTable::log();
		$this->eventLoggerTable = new EventLoggerTable;
		$this->eventLoggerTable->log();
	}

    public function __invoke(EventLoggerTable $eventLoggerTable)
    {

    }

    /*

    const LOGTYPE_LOGINAS = 1;
    const LOGTYPE_DATACHANGE = 2;

	\EventLogger\Helper\EventLogger::log();
	static function log($uid, $affected_id, $type, $description) {
	
	}

    */
}
