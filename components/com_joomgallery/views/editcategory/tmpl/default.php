<?php defined('_JEXEC') or die('Direct Access to this location is not allowed.');
echo $this->loadTemplate('header'); ?>
  <script language="javascript" type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if(document.formvalidator.isValid(document.id('adminForm'))) {
      <?php echo ';' //$this->form->getField('description')->save(); ?>
      Joomla.submitform(task, document.getElementById('adminForm'));
    }
    else {
      var msg = new Array();
      msg.push('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      if(document.adminForm.name.hasClass('invalid')) {
          msg.push('<?php echo $this->escape(JText::_("COM_JOOMGALLERY_COMMON_ALERT_CATEGORY_MUST_HAVE_TITLE"));?>');
      }
      alert(msg.join('\n'));
    }
  }
  </script>
  <div class="edit">
    <form action = "<?php echo JRoute::_('index.php?task=category.save'.$this->slimitstart); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
      <fieldset>
        <legend><?php echo (!$this->category->cid) ? JText::_('COM_JOOMGALLERY_COMMON_NEW_CATEGORY') : JText::_('COM_JOOMGALLERY_EDITCATEGORY_MODIFY_CATEGORY'); ?></legend>
        <div class="formelm">
          <?php echo $this->form->getLabel('name'); ?>
          <?php echo $this->form->getInput('name'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('alias'); ?>
          <?php echo $this->form->getInput('alias'); ?>
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('parent_id'); ?>
          <?php echo $this->form->getInput('parent_id'); ?>
        </div>
        <?php if(!$this->_config->get('jg_disableunrequiredchecks')): ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('ordering'); ?>
          <?php echo $this->form->getInput('ordering'); ?>
        </div>
        <?php endif; ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('description'); ?>
          <?php echo $this->form->getInput('description'); ?>
        </div>
<?php if(   $this->_config->get('jg_showcatthumb') >= 2
           || $this->_config->get('jg_showsubthumbs') == 1
           || $this->_config->get('jg_showsubthumbs') == 3
          ): ?>
        <div class="jg_clearboth">
          &nbsp;
        </div>
        <div class="formelm">
          <?php echo $this->form->getLabel('thumbnail'); ?>
          <?php echo $this->form->getInput('thumbnail'); ?>
        </div>
        <div class="jg_clearboth">
          &nbsp;
        </div>
<?php   if($this->_config->get('jg_usercatthumbalign')): ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('img_position'); ?>
          <?php echo $this->form->getInput('img_position'); ?>
        </div>
<?php   endif; ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('imagelib'); ?>
          <?php echo $this->form->getInput('imagelib'); ?>
        </div>
<?php endif; ?>
      </fieldset>
      <fieldset>
        <legend><?php echo JText::_('COM_JOOMGALLERY_EDITCATEGORY_PUBLISHING'); ?></legend>
        <div class="formelm">
          <?php echo $this->form->getLabel('published'); ?>
          <?php echo $this->form->getInput('published'); ?>
        </div>
<?php if($this->_config->get('jg_usercatacc')): ?>
        <div class="formelm">
          <?php echo $this->form->getLabel('access'); ?>
          <?php echo $this->form->getInput('access'); ?>
        </div>
<?php endif; ?>
      </fieldset>
<?php $fieldSets = $this->form->getFieldsets();
      foreach($fieldSets as $name => $fieldSet):
        if($name != ''): ?>
      <fieldset>
        <legend><?php echo JText::_($fieldSet->label); ?></legend>
<?php     if(isset($fieldSet->description) && trim($fieldSet->description)): ?>
        <p class="readonly"><?php echo $this->escape(JText::_($fieldSet->description)); ?></p>
<?php     endif;
          foreach($this->form->getFieldset($name) as $field): ?>
        <div class="formelm">
          <?php echo $field->label; ?>
          <?php echo $field->input; ?>
        </div>
<?php     endforeach; ?>
      </fieldset>
<?php   endif;
      endforeach; ?>
      <div class="jg_up_buttons">
        <button type="button" class="button" onclick="Joomla.submitbutton()">
          <?php echo JText::_('COM_JOOMGALLERY_COMMON_SAVE'); ?>
        </button>
        <?php $url = !empty($this->redirecturl) ? JRoute::_($this->redirecturl) : JRoute::_('index.php?view=userpanel'.$this->slimitstart, false); ?>
        <button type="button" class="button" onclick="javascript:location.href='<?php echo JRoute::_('index.php?view=usercategories'.$this->slimitstart, false); ?>';">
          <?php echo JText::_('COM_JOOMGALLERY_COMMON_CANCEL'); ?>
        </button>
      </div>
      <?php echo $this->form->getInput('cid'); ?>
    </form>
  </div>
<?php JHtml::_('behavior.formvalidation');
      JHTML::_('behavior.tooltip');
echo $this->loadTemplate('footer');