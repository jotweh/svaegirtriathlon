<?php
	/**
	 * @package Joomla
	 * @subpackage Mailster
	 * @copyright (C) 2010 Holger Brandt IT Solutions
	 * @license GNU/GPL, see license.txt
	 * Mailster is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License 2
	 * as published by the Free Software Foundation.
	 * 
	 * Mailster is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with Mailster; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
	 * or see http://www.gnu.org/licenses/.
	 */

defined('_JEXEC') or die('Restricted access');

class MstDateUtils
{
	
	public static function getDate($dbDate){
		if(!is_null($dbDate)){
			$jDate = JFactory::getDate($dbDate);
			return $jDate;
		}
		return null;
	}
	
	public static function formatDate($dbDate, $formatStr = null, $nullDateStr = '-'){
		$jDate = self::getDate($dbDate);
		if(!is_null($jDate)){
			if(is_null($formatStr)){
				return $jDate->toFormat();
			}else{
				return $jDate->toFormat($formatStr);
			}
		}
		return $nullDateStr;
	}
	
	public static function formatDateAsConfigured($dbDate, $nullDateStr = '-'){
		$mstConf = &MstFactory::getConfig();		
		$formatStr = $mstConf->getDateFormat();
		return self::formatDate($dbDate, $formatStr, $nullDateStr);
	}
	
	public static function getTimeAgo($dbDate, $nullDateStr = '', $dbDateNow = null){		
		$dbUtils = & MstFactory::getDBUtils();
		$jDate = self::getDate($dbDate);
		if(!is_null($jDate)){
			if(is_null($dbDateNow)){
				$dbDateNow = $dbUtils->getDateTimeNow();
			}
			$jDateNow = self::getDate($dbDateNow);				
			$diff =  $jDateNow->toUnix() - $jDate->toUnix();
			$timeArr = self::timeDiff2Arr($diff);
			return self::getTimeStr($timeArr, JText::_( 'COM_MAILSTER_X_TIME_UNITS_AGO' ));
		}
		return $nullDateStr;
	}
	
	private static function getTimeStr($timeArr, $stringPattern){
		$tInfo = 0;
		$tUnitStr = JText::_( 'COM_MAILSTER_SECOND' );
		
		if($timeArr['years'] > 0){
			$tInfo = $timeArr['years'];
			$tUnitStr = (($timeArr['years'] > 1) 	?  JText::_( 'COM_MAILSTER_YEARS' ) 		:  JText::_( 'COM_MAILSTER_YEAR' ));
		}elseif($timeArr['days'] > 0){
			$tInfo = $timeArr['days'];
			$tUnitStr = (($timeArr['days'] > 1) 	?  JText::_( 'COM_MAILSTER_DAYS' ) 		:  JText::_( 'COM_MAILSTER_DAY' ));
		}elseif($timeArr['hours'] > 0){
			$tInfo = $timeArr['hours'];
			$tUnitStr = (($timeArr['hours'] > 1) 	?  JText::_( 'COM_MAILSTER_HOURS' ) 		:  JText::_( 'COM_MAILSTER_HOUR' ));
		}elseif($timeArr['mins'] > 0){
			$tInfo = $timeArr['mins'];
			$tUnitStr = (($timeArr['mins'] > 1) 	?  JText::_( 'COM_MAILSTER_MINUTES' ) 	:  JText::_( 'COM_MAILSTER_MINUTE' ));
		}elseif($timeArr['secs'] > 0){
			$tInfo = $timeArr['secs'];
			$tUnitStr = (($timeArr['secs'] > 1) 	?  JText::_( 'COM_MAILSTER_SECONDS' ) 	:  JText::_( 'COM_MAILSTER_SECOND' ));
		}
		
		$tStr = $tInfo . ' ' . $tUnitStr;
		return JText::sprintf($stringPattern, $tStr);
	}
	
	private static function timeDiff2Arr($tSecs){
		$timeArr = array();
		
		$minsInSecs = 60;
		$hourInSecs = 60*$minsInSecs;
		$dayInSecs = 24*$hourInSecs;
		$yearInSecsSimplified = 365*$dayInSecs;
		
		if($tSecs > 0){
			$years 	= floor($tSecs/$yearInSecsSimplified); 
			$days 	= floor(($tSecs - ($years*$yearInSecsSimplified))/$dayInSecs);
			$hours 	= floor(($tSecs - ($years*$yearInSecsSimplified) - ($days*$dayInSecs))/$hourInSecs);
			$mins	= floor(($tSecs - ($years*$yearInSecsSimplified) - ($days*$dayInSecs) - ($hours * $hourInSecs))/$minsInSecs);
			$secs 	= ($tSecs - ($years*$yearInSecsSimplified) - ($days*$dayInSecs) - ($hours * $hourInSecs) - ($mins*$minsInSecs));
			
			$timeArr['years'] 	= $years;
			$timeArr['days']	= $days;
			$timeArr['hours']	= $hours;
			$timeArr['mins'] 	= $mins;
			$timeArr['secs']	= $secs;
		}else{
			$timeArr['years'] 	= 0;
			$timeArr['days']	= 0;
			$timeArr['hours']	= 0;
			$timeArr['mins'] 	= 0;
			$timeArr['secs']	= 0;
		}
		
		return $timeArr;
	}	
	
}
