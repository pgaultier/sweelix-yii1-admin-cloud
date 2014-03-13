<?php
/**
 * File _detail.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.group
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\admin\core\components\ElasticForm;

$dataForm = new ElasticForm($group);
?>
		<fieldset>
			<?php echo Html::activeLabel($group, 'groupTitle')?><br/>
			<?php echo Html::activeTextField($group, 'groupTitle', array('class'=>'classic'))?><br/>
			<?php echo Html::activeLabel($group, 'groupUrl')?><br/>
			<?php echo Html::activeTextField($group, 'groupUrl', array('class'=>'classic'))?><br/>
			<?php echo $dataForm->render(); ?><br/>
			<?php echo Html::resetButton(Yii::t('cloud', 'Reset'), array('class' => 'button danger'))?>
			<?php echo Html::submitButton(Yii::t('cloud', 'Ok'), array('class' => 'success'))?>
		</fieldset>
<?php
	if((isset($notice) === true) && ($notice === true)) {
		if($group->hasErrors() === false) {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('cloud', 'Info'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('cloud', 'Group details were saved'),
					'cssClass' => 'success'
			)));
		} else {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('cloud', 'Error'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('cloud', 'Group details were not saved'),
					'cssClass' => 'danger'
			)));
		}
	}
?>
