<?php
/**
 * File _itemNodeView.php
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

$class = $data->nodeStatus;
?>
<?php echo Html::openTag('tr', array(
		'class' => $class,
		'data-target' => '#'.$widget->getId(),
		'data-node-id' => $data->nodeId,
		'data-mode' => 'replace',
		'data-url-move' => Html::normalizeUrl(array('moveNode', 'nodeId'=>$data->nodeId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage))
	)); ?>

	<td class="main-id">
		<?php echo Html::checkBox('['.$index.']selected', $data->hasGroup($widget->viewData['groupId']), array('disabled'=>'disabled')); ?>
	</td>

	<td class="main-id">
		<?php
			if (Yii::app()->user->checkAccess('structure') === true) {
				echo Html::link(
					$data->nodeId,
					array('/sweeft/structure/node/', 'nodeId' => $data->nodeId ),
					array('title' => Yii::t('cloud', 'ID'))
				);
			} else {
				echo Html::tag('span', array('id', $data->nodeId), $data->nodeId);
			}
		?>
	</td>
	<td class="status">
		<?php
			if($data->nodeStatus === 'offline') {
				echo Html::tag('span', array('class' => 'icon-circle-block', 'title' => Yii::t('cloud', $data->nodeStatus)), $data->nodeStatus);
			} else {
				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('cloud', 'offline'),
							array('changeNodeStatus', 'groupId' => $groupId, 'nodeId' => $data->nodeId, 'nodeId' => $data->nodeId, 'mode' => 'offline'),
							array(
								'class' => 'icon-circle-block light inverse ajaxRefresh', 'title' => Yii::t('cloud', 'offline'),
								'data-target' => '#'.$widget->getId(),
								'data-mode' => 'replace'
							)
					);
				} else {
					echo Html::label(Yii::t('cloud', 'offline'), null,
							array(
								'class' => 'icon-circle-block light inverse', 'title' => Yii::t('cloud', 'offline'),
							)
					);
				}
			}
		?>
		<?php
			if($data->nodeStatus === 'draft') {
				echo Html::tag('span', array('class' => 'icon-file-lines', 'title' => Yii::t('cloud', $data->nodeStatus)), $data->nodeStatus);
			} else {
				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('cloud', 'draft'),
							array('changeNodeStatus', 'groupId' => $groupId, 'nodeId' => $data->nodeId, 'nodeId' => $data->nodeId, 'mode' => 'draft'),
							array(
								'class' => 'icon-file-lines light inverse ajaxRefresh', 'title' => Yii::t('cloud', 'draft'),
								'data-target' => '#'.$widget->getId(),
								'data-mode' => 'replace'
							)
					);
				} else {
					echo Html::label(Yii::t('cloud', 'draft'), null,
							array(
								'class' => 'icon-file-lines light inverse', 'title' => Yii::t('cloud', 'draft'),
							)
					);
				}
			}
		?>
		<?php
			if($data->nodeStatus === 'online') {
				echo Html::tag('span', array('class' => 'icon-circle-check', 'title' => Yii::t('cloud', $data->nodeStatus)), $data->nodeStatus);
			} else {
				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('cloud', 'online'),
							array('changeNodeStatus', 'groupId' => $groupId, 'nodeId' => $data->nodeId, 'nodeId' => $data->nodeId, 'mode' => 'online'),
							array(
								'class' => 'icon-circle-check light inverse ajaxRefresh', 'title' => Yii::t('cloud', 'online'),
								'data-target' => '#'.$widget->getId(),
								'data-mode' => 'replace'
							)
					);
				} else {
					echo Html::label(Yii::t('cloud', 'online'), null,
							array('class' => 'icon-circle-check light inverse', 'title' => Yii::t('cloud', 'online'),
							)
					);
				}
			}
		?>
	</td>
	<td>
		<?php
			if (Yii::app()->user->checkAccess('structure') === true) {
				echo Html::link(
					$data->nodeTitle,
					array('/sweeft/structure/node/', 'nodeId' => $data->nodeId ),
					array('title' => $data->nodeTitle)
				);
			} else {
				echo Html::tag('span', array('title' => $data->nodeTitle), $data->nodeTitle);
			}
		?>
	</td>
	<td class="author">
		<?php
			if (Yii::app()->user->checkAccess('users') === true) {
				echo Html::link(
					$data->author->authorFirstname.' '.$data->author->authorLastname,
					array('/sweeft/users/user/edit', 'id' => $data->author->authorId),
					array('title'=>$data->author->authorFirstname.' '.$data->author->authorLastname)
				);
			} else {
				echo Html::tag('span', array('title' => $data->author->authorFirstname.' '.$data->author->authorLastname), $data->author->authorFirstname.' '.$data->author->authorLastname);
			}
		?>
	</td>

<?php echo Html::closeTag('tr')?>