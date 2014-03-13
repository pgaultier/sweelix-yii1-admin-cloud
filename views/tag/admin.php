<?php
/**
 * File admin.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.tag
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
					array('tag/new', 'tagId'=>$tag->tagId),
					array('title'=>Yii::t('cloud', 'Create new tag'))
			);?>
		</li>
		<li>
			<?php echo Html::link(
					Yii::t('cloud', 'Create new group'),
					array('group/new', 'tagId'=>$tag->tagId),
					array('title'=>Yii::t('cloud', 'Create new group'))
			);?>
		</li>
	</ul>
		<?php $this->widget('sweelix\yii1\admin\cloud\widgets\TreeTags', array('tagId' => $tag->tagId));	?>

</nav>
<section>
	<div id="content">
		<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
	</div>
	<div id="appcontent">
		<?php echo Html::beginForm(array('tag/delete', 'tagId'=>$sourceTag->tagId)); ?>
		<fieldset>
			<label><?php echo Yii::t('cloud', 'Delete tag'); ?></label><br/>
			<?php echo Html::activeCheckBox($tag, 'selected'); ?>
			<?php echo Html::label(
				Yii::t('cloud', 'Delete tag "{tag}"',
					array(
						'{tag}'=>$sourceTag->tagTitle)
					),
					Html::activeId($tag, 'selected'));
			?><br/>
			<?php echo Html::activeHiddenField($tag, 'targetTagId'); ?>
			<?php echo Html::submitButton(Yii::t('cloud', 'Delete'), array('class' => 'medium danger')); ?><br />
			<label><?php echo Yii::t('cloud', 'View'); ?></label><br/>
			<?php
				$realUrl = Yii::app()->getModule('sweeft')->getRealSiteUrl();
				if($realUrl !== '') {
					$realUrl = $realUrl . str_replace(Yii::app()->getBaseUrl(), '', $sourceTag->getUrl());
				} else {
					$realUrl = $sourceTag->getUrl();
				}
				echo Html::link(
					Yii::t('cloud', 'Open tag'),
					$realUrl,
					array('target' => '_blank', 'class' => 'button info medium')
				);
			?>
		</fieldset>
		<?php echo Html::endForm(); ?>
	</div>
</section>
