<?php
/**
 * File admin.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.0
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.group
 */
use sweelix\yii1\web\helpers\Html;
?>
<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => $breadcrumb,
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
		<li>
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
		<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
		<?php echo Html::beginForm(array('group/delete', 'groupId'=>$sourceGroup->groupId)); ?>
		<fieldset>
			<label><?php echo Yii::t('cloud', 'Delete group'); ?></label><br/>
			<?php echo Html::activeCheckBox($group, 'selected'); ?>
			<?php echo Html::label(
				Yii::t('cloud', 'Delete group "{group}"',
					array(
						'{group}'=>$sourceGroup->groupTitle)
					),
					Html::activeId($group, 'selected'));
			?><br/>
			<?php echo Html::activeHiddenField($group, 'targetGroupId'); ?>
			<?php echo Html::submitButton(Yii::t('cloud', 'Delete'), array('class' => 'medium danger')); ?><br />
			<label><?php echo Yii::t('cloud', 'View'); ?></label><br/>
			<?php
				$realUrl = Yii::app()->getModule('sweeft')->getRealSiteUrl();
				if($realUrl !== '') {
					$realUrl = $realUrl . str_replace(Yii::app()->getBaseUrl(), '', $sourceGroup->getUrl());
				} else {
					$realUrl = $sourceGroup->getUrl();
				}
				echo Html::link(
					Yii::t('cloud', 'Open group'),
					$realUrl,
					array('target' => '_blank', 'class' => 'button info medium')
				);
			?>
		</fieldset>
		<?php echo Html::endForm(); ?>
	</div>
</section>
