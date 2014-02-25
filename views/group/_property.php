<?php
/**
 * File _property.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.group
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\ext\entities\Group;
?>
		<fieldset>
			<?php echo Html::activeLabel($group, 'groupType')?><br/>
			<?php echo Html::activeRadioButtonList($group, 'groupType', Group::getAvailableTypes(), array('separator'=>' ', 'labelOptions'=>array('class'=>'fixed')))?><br/>
			<?php echo Html::activeLabel($group, 'templateId')?><br/>
			<?php echo Html::activeDropDownList($group, 'templateId', Html::listData($templates, 'templateId', 'templateTitle'), array('class'=>'classic'))?><br/>

			<?php echo Html::resetButton(Yii::t('cloud', 'Reset'), array('class' => 'button danger'))?>
			<?php echo Html::submitButton(Yii::t('cloud', 'Validate'), array('class' => 'success'))?>
		</fieldset>
<?php
	if((isset($notice) === true) && ($notice === true)) {
		if($group->hasErrors() === false) {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('cloud', 'Info'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('cloud', 'Group properties were saved'),
					'cssClass' => 'success'
			)));
		} else {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('cloud', 'Error'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('cloud', 'Group properties were not saved'),
					'cssClass' => 'danger'
			)));
		}
	}
?>
