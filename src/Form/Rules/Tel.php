<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Form\Rule;

/**
 * Form Rule class for telephone
 */
class Tel extends Rule
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   object   &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed    $value     The form field value to validate.
	 * @param   string   $group     The field name group control value. This acts as as an array container for the field.
	 *                              For example if the field has name="foo" and the group value is set to "bar" then the
	 *                              full field name would end up being "bar[foo]".
	 * @param   object   &$input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   object   &$form     The form object for which the field is being tested.
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

		if (!$required && empty($value))
		{
			return true;
		}

		// @see http://www.nanpa.com/
		// @see http://tools.ietf.org/html/rfc4933
		// @see http://www.itu.int/rec/T-REC-E.164/en

		// Regex by Steve Levithan
		// @see http://blog.stevenlevithan.com/archives/validate-phone-number
		// @note that valid ITU-T and EPP must begin with +.
		$regexarray = array(
			'NANP'  => '/^(?:\+?1[-. ]?)?\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/',
			'ITU-T' => '/^\+(?:[0-9] ?){6,14}[0-9]$/',
			'EPP'   => '/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/'
		);

		if (isset($element['plan']))
		{

			$plan = (string) $element['plan'];
			if ($plan == 'northamerica' || $plan == 'us')
			{
				$plan = 'NANP';
			}
			elseif ($plan == 'International' || $plan == 'int' || $plan == 'missdn' || !$plan)
			{
				$plan = 'ITU-T';
			}
			elseif ($plan == 'IETF')
			{
				$plan = 'EPP';
			}

			$regex = $regexarray[$plan];
			// Test the value against the regular expression.
			if (preg_match($regex, $value) == false)
			{

				return false;
			}
		}
		else
		{
			// If the rule is set but no plan is selected just check that there are between
			// 7 and 15 digits inclusive and no illegal characters (but common number separators
			// are allowed).
			$cleanvalue = preg_replace('/[+. \-(\)]/', '', $value);
			$regex = '/^[0-9]{7,15}?$/';
			if (preg_match($regex, $cleanvalue) == true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		return true;
	}
}
