<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Ingmar Schlecht (ingmar@typo3.org)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class tx_advertiser_evalfield_price {
	/**
	 * This function needs to return JavaScript code for client side evaluation of the
	 * field value. The JavaScript variable "value" is set to the field value in the context
	 * of this JS snippet.
	 * In this example we just add the string "[added by JS]" to the field value.
	 *
	 * @return	JavaScript code for evaluating the 
	 */
	function returnFieldJS() {
	        //  field 'price' has type 'text' and will not be touched by this function (but why???). We only return the value
		return '
			return value;
		';
	}

	/**
	 * This is the server side (i.e. PHP) side of the field evaluation.
	 * In this example we just add the string "[added by PHP]" to the field value.
	 *
	 * @param	mixed		$value: The value that has to be checked.
	 * @param	string		$is_in: Is-In String
	 * @param	integer		$set: Determines if the field can be set (value correct) or not (PASSED BY REFERENCE!)
	 * @return	The new value of the field
	 */
	function evaluateFieldValue($value, $is_in, &$set) {
			//  split lines
		$value = str_replace("\r\n", "\n", $value);
		$value = str_replace("\n\r", "\n", $value);
		$value = str_replace("\r", "\n", $value);
		$lines = explode("\n", $value);

			//  eval values
        foreach ($lines as $lKey => $lVal) {
            $lVal = trim($lVal);
			$lVal = preg_replace('/[^0-9,\.-]/', '', $lVal);
			$lVal = preg_replace('/,/', '.', $lVal);
			$lVal = (float)$lVal;
			$lVal = round($lVal, 2);
			$lVal = number_format($lVal, 2, '.', '');
			$lines[$lKey] = $lVal;
		}
		$value = implode("\n", $lines);

		return $value;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/example_extraevaluations/class.tx_exampleextraevaluations_extraeval1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/example_extraevaluations/class.tx_exampleextraevaluations_extraeval1.php']);
}
?>