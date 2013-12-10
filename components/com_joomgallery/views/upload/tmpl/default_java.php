<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
<div class="width-100">
  <fieldset class="adminform">
    <legend><?php echo JText::_('COM_JOOMGALLERY_COMMON_IMPORTANT_NOTICE'); ?></legend>
    <span class="readonly"><?php echo JText::_('COM_JOOMGALLERY_UPLOAD_JUPLOAD_NOTE'); ?></span>
  </fieldset>
</div>
<form action="<?php echo JRoute::_('index.php?type=java'); ?>" method="post" name="JavaUploadForm" id="JavaUploadForm" enctype="multipart/form-data" class="form-validate" onsubmit="if(this.task.value == 'upload.upload' && !document.formvalidator.isValid(document.id('JavaUploadForm'))){alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true); ?>');return false;}">
  <div>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('catid'); ?>
      <?php echo $this->applet_form->getInput('catid'); ?>
    </div>
      <?php if(!$this->_config->get('jg_useruseorigfilename')): ?>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('imgtitle'); ?>
      <?php echo $this->applet_form->getInput('imgtitle'); ?>
    </div>
      <?php endif; ?>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('imgtext'); ?>
      <?php echo $this->applet_form->getInput('imgtext'); ?>
    </div>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('imgauthor'); ?>
      <b><?php echo JHTML::_('joomgallery.displayname', $this->_user->get('id'), 'upload'); ?></b>
    </div>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('published'); ?>
      <?php echo $this->applet_form->getInput('published'); ?>
    </div>
    <?php /*<div class="formelm">
      <?php echo $this->applet_form->getLabel('access'); ?>
      <?php echo $this->applet_form->getInput('access'); ?>
    </div> */ ?>
      <?php if($this->_config->get('jg_delete_original_user') == 2): ?>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('original_delete'); ?>
      <?php echo $this->applet_form->getInput('original_delete'); ?>
    </div>
      <?php endif;
            if($this->_config->get('jg_special_gif_upload') == 1): ?>
    <div class="formelm">
      <?php echo $this->applet_form->getLabel('create_special_gif'); ?>
      <?php echo $this->applet_form->getInput('create_special_gif'); ?>
    </div>
      <?php endif; ?>
    <div class="formelm">
      <?php echo $this->applet_form->getInput('applet'); ?>
    </div>
    <!--<div class="formelm">
      <label for="button"></label>
      <button id="button" type="button" onclick="if(!document.formvalidator.isValid(document.id('JavaUploadForm'))){alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true); ?>');return false;}document.JUpload.startUpload();"><?php echo JText::_('COM_JOOMGALLERY_UPLOAD_UPLOAD'); ?></button>
    </div>-->
    <input type="hidden" name="task" value="upload.upload" />
  </div>
</form>