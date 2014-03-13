<?php
/**
 * File step2.php
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

Yii::app()->getClientScript()->registerSweelixScript('callback');
Yii::app()->getModule('sweeft')->registerWysiwygEditor();
?>
<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => array(
		array(
			'content' => Yii::t('cloud', 'Step {n}', array('{n}' => 1 )),
			'url' => array('step1', 'tagId' => $tag->tagId),
		),
		array(
			'content' => Yii::t('cloud', 'Step {n}', array('{n}' => 2 )),
		),
	)
)); ?>

<nav>
	<br><br><br>
	<ul class="shortcuts">
		<li class="active">
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
	<?php $this->widget('sweelix\yii1\admin\cloud\widgets\TreeTags', array('tagId'=>$tag->tagId)); ?>
</nav>

<section>
	<div id="content">
		<?php
			echo Html::beginAjaxForm('','post',array('enctype'=>'multipart/form-data'));
		?>
		<?php $this->renderPartial('_detail', array('tag' => $tag)); ?>
		<?php echo Html::endForm(); ?>
	</div>
</section>
