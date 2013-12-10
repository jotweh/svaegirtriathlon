// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-2.0/JG/trunk/media/joomgallery/js/maintenance.js $
// $Id: maintenance.js 3651 2012-02-19 14:36:46Z mab $
/****************************************************************************************\
**   JoomGallery  2                                                                     **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2008 - 2012  JoomGallery::ProjectTeam                                **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                            **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

function joom_selectnewuser(id)
{
  if(document.adminForm.tab.value == 'categories')
  {
    var task = 'setcategoryuser';
  }
  else
  {
    var task = 'setuser';
  }

  $('newuser').injectInside('correctuser' + id);
  $('filesave').injectInside('correctuser' + id);
  $('filesave').removeEvents();
  $('filesave').addEvent('click', function(){
    listItemTask('cb' + id, task);
  });
}

function joom_selectbatchjob(job)
{
  if(job == 'setuser')
  {
    $('newuser').injectInside('batchjobs');
    $('usertip').injectInside('batchjobs');
    $('filesave').injectInside('garage');
  }
  else
  {
    $('newuser').injectInside('garage');
    $('usertip').injectInside('garage');
    $('filesave').injectInside('garage');
  }

  if(document.adminForm.tab.value == 'categories' && job == 'setuser')
  {
    document.adminForm.task.value = 'setcategoryuser';
  }
  else
  {
    document.adminForm.task.value = job;
  }
}