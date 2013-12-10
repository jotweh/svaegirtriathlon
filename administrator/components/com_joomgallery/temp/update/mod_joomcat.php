<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/Module/JoomCategories/trunk/mod_joomcat.php $
// $Id: mod_joomcat.php 3396 2011-10-12 17:07:06Z erftralle $
/**
* Module JoomCategories for JoomGallery
* by JoomGallery::Project Team
* @package JoomGallery
* @copyright JoomGallery::Project Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the Free Software
* Foundation, either version 2 of the License, or (at your option) any later
* version.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

$jg_installed = null;

if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'interface.php'))
{
  // Include JoomGallery's interface class
  require_once JPATH_ROOT.DS.'components'.DS.'com_joomgallery'.DS.'interface.php';

  // Include the helper functions only once
  require_once dirname(__FILE__).DS.'helper.php';

  // Create an instance of the helper object
  $jc_obj = new modJoomCatHelper();

  // Check gallery version
  if($jc_obj->getGalleryVersion() >= '2.0')
  {
    // Correct version of JoomGallery seems to be installed
    $jg_installed = true;

    // Get the categories from JoomGallery
    $cat_rows = $jc_obj->fillObject($params, $dberror, $module->id);
  }
}

// Show the categories
require(JModuleHelper::getLayoutPath('mod_joomcat'));