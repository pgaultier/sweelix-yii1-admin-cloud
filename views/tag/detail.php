<?php
/**
 * File detail.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.cloud.views.tag
 */
use sweelix\yii1\web\helpers\Html;

Yii::app()->getClientScript()->registerSweelixScript('callback');
Yii::app()->getModule('sweeft')->registerWysiwygEditor();
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
			<?php
				echo Html::beginAjaxForm('','post',array('enctype'=>'multipart/form-data'));
			?>
			<?php $this->renderPartial('_detail', array(
						'tag' => $tag,
						'notice' => (isset($notice)?$notice:false),
			))?>
			<?php echo Html::endForm(); ?>
		</div>
</section>
