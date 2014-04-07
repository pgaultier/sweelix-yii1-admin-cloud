<?php
/**
 * File _footerView.php
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

$currentPage = $widget->dataProvider->pagination->currentPage;
$previousPage = max($currentPage - 1, 0);
$nexPage = min($currentPage + 1, $widget->dataProvider->pagination->pageCount - 1);

?>
<tr>
	<th colspan="6">
		<?php if($currentPage > 0) echo Html::link('<', [$route, 'tagId' => $tagId, 'page' => $previousPage], ['class' => 'button small']);?>
		<?php if($widget->dataProvider->totalItemCount > 0):?>
			<span class="in-button"><?php echo(Yii::t('cloud', $title, [$widget->dataProvider->totalItemCount, '{pageNum}' => ($currentPage + 1), '{pageCount}' => $widget->dataProvider->pagination->pageCount]));?></span>
		<?php endif;?>
		<?php if($currentPage < ($widget->dataProvider->pagination->pageCount-1)) echo Html::link('>', [$route, 'tagId' => $tagId, 'page' => $nexPage], ['class' => 'button small']);?>
	</th>
</tr>