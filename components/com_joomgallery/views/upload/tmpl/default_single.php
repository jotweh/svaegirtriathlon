<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>
<form action="<?php echo JRoute::_('index.php?type=single'); ?>" method="post" name="adminForm" id="SingleUploadForm" enctype="multipart/form-data" class="form-validate" onsubmit="if(this.task.value == 'upload.upload' && !document.formvalidator.isValid(document.id('SingleUploadForm'))){alert('<?php echo JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true); ?>');return false;} return joomOnSubmit();">
  <div>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('catid'); ?>
      <?php echo $this->single_form->getInput('catid'); ?>
    </div>
      <?php if(!$this->_config->get('jg_useruseorigfilename')): ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('imgtitle'); ?>
      <?php echo $this->single_form->getInput('imgtitle'); ?>
    </div>
      <?php endif;
            if(!$this->_config->get('jg_useruseorigfilename') && $this->_config->get('jg_useruploadnumber')): ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('filecounter'); ?>
      <?php echo $this->single_form->getInput('filecounter'); ?>
    </div>
      <?php endif; ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('imgtext'); ?>
      <?php echo $this->single_form->getInput('imgtext'); ?>
    </div>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('imgauthor'); ?>
      <b><?php echo JHTML::_('joomgallery.displayname', $this->_user->get('id'), 'upload'); ?></b>
    </div>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('published'); ?>
      <?php echo $this->single_form->getInput('published'); ?>
    </div>
    <?php /*<div class="formelm">
      <?php echo $this->single_form->getLabel('access'); ?>
      <?php echo $this->single_form->getInput('access'); ?>
    </div> */ ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('arrscreenshot'); ?>
      <?php echo $this->single_form->getInput('arrscreenshot'); ?>
    </div>
      <?php if($this->_config->get('jg_delete_original_user') == 2): ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('original_delete'); ?>
      <?php echo $this->single_form->getInput('original_delete'); ?>
    </div>
      <?php endif;
            if($this->_config->get('jg_special_gif_upload') == 1): ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('create_special_gif'); ?>
      <?php echo $this->single_form->getInput('create_special_gif'); ?>
    </div>
      <?php endif;
            if($this->_config->get('jg_redirect_after_upload')): ?>
    <div class="formelm">
      <?php echo $this->single_form->getLabel('debug'); ?>
      <?php echo $this->single_form->getInput('debug'); ?>
    </div>
      <?php endif; ?>
    <div class="formelm">
      <label for="button"></label>
      <button type="submit" class="button"><?php echo JText::_('COM_JOOMGALLERY_UPLOAD_UPLOAD'); ?></button>
      <button type="button" class="button" onclick="javascript:location.href='<?php echo JRoute::_('index.php?view=userpanel', false); ?>';return false;"><?php echo JText::_('COM_JOOMGALLERY_COMMON_CANCEL'); ?></button>
    </div>
    <input type="hidden" name="task" value="upload.upload" />
  </div>
</form>