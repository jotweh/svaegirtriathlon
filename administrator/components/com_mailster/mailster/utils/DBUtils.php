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

class MstDBUtils
{
	
	public static function getDateTimeNow(){
		$db = self::getDB();
		$query = 'SELECT NOW()';
		$db->setQuery($query);
		$result = $db->query();
		$dateTime = $db->loadResult();
		return $dateTime;
	}
	
	public static function userTableCollationOk(){
		$cEmailCollation = self::getCollation('#__users', 'email');
		$cNameCollation = self::getCollation('#__users', 'name');
		$mEmailCollation = self::getCollation('#__mailster_users', 'email');
		$mNameCollation = self::getCollation('#__mailster_users', 'name');
		return ( ($cEmailCollation === $mEmailCollation) && ($cNameCollation === $mNameCollation) );
	}
	
	public static function getCollation($tableName, $field=null){
		$db = self::getDB();
		$query = 'SHOW FULL COLUMNS FROM ' . $tableName . ' WHERE 1';
		$db->setQuery($query);
		$result = $db->query();	
		if(is_null($field)){
			$array = $db->loadObjectList('Field');
			foreach($array as $key=>$column){
				$collation = $column->Collation;
				if(!is_null($collation) && (strlen($collation) > 0) ){
					return $collation;
				}
			}
			return print_r($array, true);
		}else{		
			$array = $db->loadObjectList('Field');
			return $array[$field]->Collation;
		}		
	}
	
	public static function alterCollation($tableName, $field, $newCollation){
		$log = & MstFactory::getLogger();
		$db = self::getDB();
		$colExists = false;
		$colData = self::getTableColumns($tableName);		
		foreach ($colData as $valCol) {
			if ($valCol->Field == $field) {
				$colExists = true;
				$log->debug(print_r($valCol, true));
				$definition = $valCol->Type . ' ';
				if($valCol->Default){
					$defaultVal = ' DEFAULT ' . $valCol->Default;
				}else{
					$defaultVal = ' ';
				}
				if(strtoupper($valCol->Null) === 'YES'){
					$nullVal = 'NULL';
				}else{
					$nullVal = 'NOT NULL';
					if($valCol->Key && $valCol->Key !== ''){
						$defaultVal = ' ';
					}
				}
				$definition .= $nullVal . $defaultVal . ' ' . $valCol->Extra;				
				break;
			}
		}		
		if ($colExists) {			
			$query = 'ALTER TABLE '.$db->nameQuote($tableName).' CHANGE '.$db->nameQuote($field).' '.$db->nameQuote($field).' '.$definition.' COLLATE '.$newCollation;
			$db->setQuery( $query );
			if (!$result = $db->query()){
				$errorMsg =  $db->getErrorMsg();
				$log->error('Error while changing collation from ' . $field . ' to ' . $newCollation . ' in ' . $tableName . ', Message: ' . $errorMsg);
				echo '<p>' . $errorMsg . '</p>';
				return -1;
			}	
			$log->info('Changed collation of ' . $field . ' to ' . $newCollation . ' in ' . $tableName );
			return 1;
		}	
		$log->info('' . $field . ' not in ' . $tableName . ', not changing collation to ' . $newCollation);
		return 0;	
	}
	
	public static function isColExisting($tblName, $col){
		$colExists = false;
		$colData = self::getTableColumns($tblName);	
		foreach ($colData as $valCol) {
			if ($valCol->Field == $col) {
				$colExists = true;				
			}
		}
		return $colExists;
	}
	
	public static function isTableExisting($tblName){
		$db = self::getDB();
		$query = 'SHOW TABLES LIKE \''.$tblName.'\'';
		$db->setQuery( $query );
		if (!$result = $db->query()){
			return false;
		}
		if($db->getNumRows() > 0){
			return true;
		}else{
			return false;
		}
	}
	
	public static function deleteCol($tblName, $col){
		$db = self::getDB();
		$query = 'ALTER TABLE ' . $tblName . ' DROP ' . $col;
		$db->setQuery( $query );
		if (!$result = $db->query()){
			echo $db->getErrorMsg();
			return false;
		}		
		return true;
	}
	
	public static function deleteColIfExists($tblName, $col){
		$colExists = self::isColExisting($tblName, $col);
		if($colExists){
			return self::deleteCol($tblName, $col);
		}
		return 0;
	}
	
	public static function getTableColumns($tblName){
		$db = self::getDB();
		$query = 'SHOW COLUMNS FROM '.$tblName;
		$db->setQuery( $query );
		if (!$result = $db->query()){
			echo $db->getErrorMsg();
			return -1;
		}		
		return $db->loadObjectList();	
	}
	
	
	public static function alterDefaultValue($table, $col, $newDefault){
		$log = & MstFactory::getLogger();
		$db = self::getDB();
		$colExists 	= false;
		$defaultNeedsAltering 	= false;	
		$colData = self::getTableColumns($table);		
		foreach ($colData as $valCol) {
			if ($valCol->Field == $col) {
				$colExists = true;
				if($valCol->Default){
					if($valCol->Default != $newDefault){
						$defaultNeedsAltering = true;
					}
				}else{
					$defaultNeedsAltering = true;
				}
			}
		}
				
		if ($colExists && $defaultNeedsAltering) {			
			$query = 'ALTER TABLE '.$db->nameQuote($table).' ALTER '.$db->nameQuote($col).' SET DEFAULT '.$newDefault;		
			$db->setQuery( $query );
			if (!$result = $db->query()){
				$errorMsg =  $db->getErrorMsg();
				$log->error('Error while altering default of ' . $col . ' of table ' . $table . ' to: ' . $newDefault);
				echo '<p>' . $errorMsg . '</p>';
				return -1;
			}	
			$log->info('Cannot alter default value -> column'  .  $col . ' is not in ' . $table);
			return 1;
		}	
		$log->info('Column ' . $col . ' already in ' . $table);
		return 0;
		
	}
	
	public static function addColIfNotExists($table, $col, $atts, $afterCol ) {		
		$log = & MstFactory::getLogger();
		$db = self::getDB();
		$colExists 	= false;
		$colData = self::getTableColumns($table);		
		foreach ($colData as $valCol) {
			if ($valCol->Field == $col) {
				$colExists = true;
				break;
			}
		}		
		if (!$colExists) {			
			$query = 'ALTER TABLE '.$db->nameQuote($table).' ADD '.$db->nameQuote($col).' '.$atts.' AFTER '.$db->nameQuote($afterCol);		
			$db->setQuery( $query );
			if (!$result = $db->query()){
				$errorMsg =  $db->getErrorMsg();
				$log->error('Error while adding ' . $col . ' to ' . $table . ', Message: ' . $errorMsg);
				echo '<p>' . $errorMsg . '</p>';
				return -1;
			}	
			$log->info('Added ' . $col . ' to ' . $table);
			return 1;
		}	
		$log->info('Column ' . $col . ' already in ' . $table);
		return 0;
	}
	
	public static function changeColType($table, $col, $newDefinition ) {	
		$log = & MstFactory::getLogger();
		$db = self::getDB();
		$colExists 	= false;
		$colTypeDifferent = false;
		$colData = self::getTableColumns($table);		
		foreach ($colData as $valCol) {
			if ($valCol->Field == $col) {
				$colExists = true;
				if(trim(strtolower($valCol->Type)) !== (trim(strtolower($newDefinition)))){
					$colTypeDifferent = true;
				}
				$definition = $valCol->Type . ' ';
				if($valCol->Default){
					$defaultVal = ' DEFAULT ' . $valCol->Default;
				}else{
					$defaultVal = ' ';
				}
				if(strtoupper($valCol->Null) === 'YES'){
					$nullVal = 'NULL';
				}else{
					$nullVal = 'NOT NULL';
					if($valCol->Key && $valCol->Key !== ''){
						$defaultVal = ' ';
					}
				}
				$definition .= $nullVal . $defaultVal . ' ' . $valCol->Extra;				
				break;
			}
		}	
		if ($colExists) {	
			if($colTypeDifferent){
				$query = 'ALTER TABLE '.$db->nameQuote($table).' MODIFY '.$db->nameQuote($col). ' ' . $newDefinition;
				$db->setQuery( $query );
				if (!$result = $db->query()){
					$errorMsg =  $db->getErrorMsg();
					$log->error('Error while modifying column ' . $col . ' to ' . $newDefinition . ' in ' . $table . ', Message: ' . $errorMsg);
					echo '<p>' . $errorMsg . '</p>';
					return -1;
				}	
				$log->info('Modified ' . $col . ' to ' . $newDefinition . ' (previously: ' .  $valCol->Type . ') in ' . $table );
				return 1;
			}
			$log->info('' . $col . ' type is already ' . $newDefinition  . ', no need to modify');
			return 0;
		}	
		$log->info('' . $col . ' not in ' . $table . ', not modifying to ' . $newDefinition);
		return 0;
	}
	
	
	public static function renameCol($table, $oldName, $newName ) {	
		$log = & MstFactory::getLogger();
		$db = self::getDB();
		$colExists 	= false;
		$colData = self::getTableColumns($table);		
		foreach ($colData as $valCol) {
			if ($valCol->Field == $oldName) {
				$colExists = true;
				$definition = $valCol->Type . ' ';
				if($valCol->Default){
					$defaultVal = ' DEFAULT ' . $valCol->Default;
				}else{
					$defaultVal = ' ';
				}
				if(strtoupper($valCol->Null) === 'YES'){
					$nullVal = 'NULL';
				}else{
					$nullVal = 'NOT NULL';
					if($valCol->Key && $valCol->Key !== ''){
						$defaultVal = ' ';
					}
				}
				$definition .= $nullVal . $defaultVal . ' ' . $valCol->Extra;				
				break;
			}
		}		
		if ($colExists) {			
			$query = 'ALTER TABLE '.$db->nameQuote($table).' CHANGE '.$db->nameQuote($oldName).' '.$db->nameQuote($newName).' '.$definition;
			$db->setQuery( $query );
			if (!$result = $db->query()){
				$errorMsg =  $db->getErrorMsg();
				$log->error('Error while renaming ' . $oldName . ' to ' . $newName . ' in ' . $table . ', Message: ' . $errorMsg);
				echo '<p>' . $errorMsg . '</p>';
				return -1;
			}	
			$log->info('Renamed ' . $oldName . ' to ' . $newName . ' in ' . $table );
			return 1;
		}	
		$log->info('' . $oldName . ' not in ' . $table . ', not renaming to ' . $newName);
		return 0;
	}
	
	public static function createIndexIfNotExists($table, $indexName, $cols, $type='INDEX'){
		$log = & MstFactory::getLogger();	
		$db = self::getDB();
		$indexExists = false;
		$query = 'SHOW INDEXES FROM ' . $table;
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		for($i=0; $i < count($result); $i++){
			$currIndex = &$result[$i];
			$currIndexName = $currIndex->Key_name;
			if($currIndexName == $indexName){
				$indexExists = true;
				break;
			}
		}
		
		$query = '';
		
		if(!$indexExists){
		
			$query = 'ALTER TABLE ' . $table . ' ADD INDEX ' . $indexName . ' ( ' . $cols[0];
			for($i=1; $i < count($cols); $i++){
				$query .= ', ' . $cols[$i];
			}
			$query .= ')';
			$db->setQuery( $query );
			if (!$result = $db->query()){
				$errorMsg =  $db->getErrorMsg();
				$log->error('Error while creating index ' . $indexName . ' in ' . $table . ', columns: ' . print_r($cols, true) . ', Message: ' . $errorMsg);
				echo '<p>' . $errorMsg . '</p>';
				return -1;
			}	
			$log->info('Created index ' . $indexName . ' in ' . $table );
			return 1;
		}		
		$log->info('Index ' . $indexName . ' already in ' . $table );
		return 0;
	}
	
	private static function getDB(){
		$db =& JFactory::getDBO();
		return $db;
	}
}
