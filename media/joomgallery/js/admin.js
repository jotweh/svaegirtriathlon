// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/JG/trunk/media/joomgallery/js/admin.js $
// $Id: admin.js 3651 2012-02-19 14:36:46Z mab $
/****************************************************************************************\
**   JoomGallery  2                                                                     **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2012  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

/**
 * Overwrite function for Joomla.submitbutton.
 * Redirects to CPanel or submits the form. 
 *  
 * @param   string  pressbutton The button pressed
 * @return  void
 */
Joomla.submitbutton = function(pressbutton)
{
  if (pressbutton == 'cpanel') {
    location.href = 'index.php?option=com_joomgallery';
  } else {
    Joomla.submitform(pressbutton);
  }
};