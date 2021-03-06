<?php
/**
 * File _listContent.php
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

?>
<?php
	//XXX: if we do not set the id, there is a problem during ajax calls because we do not reload the whole page (ie: we do not recalculate all the widgets ids).
	$this->widget('sweelix\yii1\ext\web\widgets\ListView', array(
		'dataProvider' => $contentsDataProvider,
		'id' => 'contentsList',
		'tagName' => 'table',
		'itemView' => '_itemView',
		'headerView' => '_headerView',
		'footerView' => '_footerView',
		// 'summaryView' => '_summaryView',
		'template' => "\n<thead>\n{header}\n{summary}\n</thead>\n<tbody>\n{items}\n</tbody>\n<tfoot>\n{footer}\n</tfoot>\n",
		'viewData' => array(
			'tagId' => $tag->tagId,
			'route' => 'tag/listContent',
			'title' => 'Page {pageNum} / {pageCount}',
		),
	));
?>
<?php if((isset($withoutButtons) === false) || ($withoutButtons === false)) :?>
		<?php echo Html::resetButton(Yii::t('cloud', 'Reset'), array('class' => 'button danger'))?>
		<?php echo Html::submitButton(Yii::t('cloud', 'Ok'), array('class' => 'success'))?>
<?php endif; ?>

