<?php
/**
 * File _itemView.php
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

$class = $data->contentStatus;
if($data->isPublishable(false) === false) {
	$class = $class." unpublished";
}
?>
<?php echo Html::openTag('tr', array(
		'class' => $class,
		'data-target' => '#'.$widget->getId(),
		'data-content-id' => $data->contentId,
		'data-mode' => 'replace',
		'data-url-move' => Html::normalizeUrl(array('moveContent', 'contentId'=>$data->contentId, 'nodeId'=>$data->nodeId))
	)); ?>

	<td class="main-id">
		<?php echo Html::checkBox('['.$index.']selected', $data->hasGroup($widget->viewData['groupId']), array('disabled'=>'disabled')); ?>
	</td>

	<td class="main-id">
		<?php
		if (Yii::app()->user->checkAccess('structure') === true) {
			echo Html::link(
				$data->contentId,
				array('/sweeft/structure/content/', 'contentId' => $data->contentId ),
				array('title' => Yii::t('CloudModule.sweelix', 'ID'))
			);
		} else {
			echo Html::tag('span', array('id' => $data->contentId), $data->contentId);
		}
		?>
	</td>

	<td class="status">
		<?php
			if($data->contentStatus === 'offline') {
				echo Html::tag('span', array('class' => 'icon-circle-block', 'title' => Yii::t('CloudModule.sweelix', $data->contentStatus)), $data->contentStatus);
			} else {
				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('CloudModule.sweelix', 'offline'),
						array('changeContentStatus', 'groupId' => $groupId, 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'offline'),
						array(
							'class' => 'icon-circle-block light inverse ajaxRefresh', 'title' => Yii::t('CloudModule.sweelix', 'offline'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
					);
				} else {
					echo Html::label(Yii::t('CloudModule.sweelix', 'offline'), null,
							array(
								'class' => 'icon-circle-block light inverse', 'title' => Yii::t('CloudModule.sweelix', 'offline'),
							)
					);
				}
			}
		?>
		<?php
			if($data->contentStatus === 'draft') {
				echo Html::tag('span', array('class' => 'icon-file-lines', 'title' => Yii::t('CloudModule.sweelix', $data->contentStatus)), $data->contentStatus);
			} else {
				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('CloudModule.sweelix', 'draft'),
						array('changeContentStatus', 'groupId' => $groupId, 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'draft'),
						array(
							'class' => 'icon-file-lines light inverse ajaxRefresh', 'title' => Yii::t('CloudModule.sweelix', 'draft'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
					);
				} else {
					echo Html::label(Yii::t('CloudModule.sweelix', 'draft'), null,
							array(
								'class' => 'icon-file-lines light inverse', 'title' => Yii::t('CloudModule.sweelix', 'draft'),
							)
					);
				}
			}
		?>
		<?php
			if($data->contentStatus === 'online') {
				echo Html::tag('span', array('class' => 'icon-circle-check', 'title' => Yii::t('CloudModule.sweelix', $data->contentStatus)), $data->contentStatus);
			} else {
 				if (Yii::app()->user->checkAccess('structure') === true) {
					echo Html::link(Yii::t('CloudModule.sweelix', 'online'),
						array('changeContentStatus', 'groupId' => $groupId, 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'online'),
						array(
							'class' => 'icon-circle-check light inverse ajaxRefresh', 'title' => Yii::t('CloudModule.sweelix', 'online'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
					);
				} else {
					echo Html::label(Yii::t('CloudModule.sweelix', 'online'), null,
							array('class' => 'icon-circle-check light inverse', 'title' => Yii::t('CloudModule.sweelix', 'online'),
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
					$data->contentTitle,
					array('/sweeft/structure/content/', 'contentId' => $data->contentId),
					array('title' => $data->contentTitle)
				);
			} else {
				echo Html::tag('span', array('title' => $data->contentTitle), $data->contentTitle);
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