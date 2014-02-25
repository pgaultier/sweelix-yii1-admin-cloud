<?php
/**
 * File step1.php
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
?>
<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => array(
		array(
			'content' => Yii::t('cloud', 'Step {n}', array('{n}' => 1 )),
			// 'url' => array('step1', 'nodeId' => $sourceNode->nodeId),
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
	<div id="appcontent">
		<?php echo Html::beginAjaxForm();	?>
			<?php $this->renderPartial('_property', array('group'=>$group, 'templates'=>$templates))?>
		<?php echo Html::endForm(); ?>
	</div>
</section>
