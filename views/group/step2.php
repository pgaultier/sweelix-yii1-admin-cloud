<?php
/**
 * File step2.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.group
 */
use sweelix\yii1\web\helpers\Html;

Yii::app()->getClientScript()->registerSweelixScript('callback');
Yii::app()->getModule('sweeft')->registerWysiwygEditor();
?>
<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
		'elements' => array(
				array(
					'content' => Yii::t('cloud', 'Step {n}', array('{n}' => 1 )),
					'url' => array('step1', 'groupId' => $group->groupId),
				),
				array(
					'content' => Yii::t('cloud', 'Step {n}', array('{n}' => 2 )),
				),
		)
)); ?>
<nav>
	<br><br><br>
	<ul class="shortcuts">
		<li>
			<?php echo Html::link(
				Yii::t('cloud', 'Create new tag'),
				array('tag/new', 'groupId'=>$group->groupId),
				array('title'=>Yii::t('cloud', 'Create new tag'))
			);?>
		</li>
		<li class="active">
			<?php echo Html::link(
				Yii::t('cloud', 'Create new group'),
				array('group/new', 'groupId'=>$group->groupId),
				array('title'=>Yii::t('cloud', 'Create new group'))
			);?>
		</li>
	</ul>
	<?php $this->widget('sweelix\yii1\admin\cloud\widgets\TreeTags', array('groupId'=>$group->groupId)); ?>
</nav>

<section>
	<div id="content">
		<?php echo Html::beginAjaxForm('','post',array('enctype'=>'multipart/form-data')); ?>
			<?php $this->renderPartial('_detail', array(
					'group' => $group,
			)); ?>
		<?php echo Html::endForm(); ?>
	</div>
</section>
