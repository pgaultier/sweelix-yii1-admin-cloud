<?php
/**
 * File TreeTags.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  widgets
 * @package   sweelix.yii1.admin.cloud.widgets
 */

namespace sweelix\yii1\admin\cloud\widgets;
use sweelix\yii1\ext\entities\Tag;
use sweelix\yii1\ext\db\CriteriaBuilder;
use sweelix\yii1\web\helpers\Html;

/**
 * Class TreeTags
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  widgets
 * @package   sweelix.yii1.admin.cloud.widgets
 */
class TreeTags extends \CWidget {
	/**
	 * @var integer id of current group
	 */
	private $_groupId;

	/**
	 * @param integer $groupId group id
	 */
	public function setGroupId($groupId) {
		$this->_groupId = $groupId;
	}

	/**
	 * @return integer $groupId group id
	 */
	public function getGroupId() {
		if(($this->_groupId === null) && ($this->tagId !== null)) {
			$tag = Tag::model()->findByPk($this->tagId);
			if($tag !== null) {
				$this->_groupId = $tag->groupId;
			}
		}
		return $this->_groupId;
	}
	public $tagId;
	/**
	 * @var $_groups collection list of groups
	 */
	private $_groups = null;


	/**
	 * Init widget
	 * Called by CController::beginWidget()
	 *
	 * @return void
	 */
	public function init() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.widgets');
		$criteriaBuilder = new CriteriaBuilder('group');
		$criteriaBuilder->orderBy('groupTitle');
		$this->_groups = $criteriaBuilder->findAll();
	}

	/**
	 * Render widget
	 * Called by CController::endWidget()
	 *
	 * @return void
	 */
	public function run() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.widgets');
		echo Html::tag(
			'div',
			array('id'=>'treemenu'),
			'<div class="masking"></div>'.
			$this->groupsToHtml()
		);
	}

	/**
	 * Render tree structure
	 *
	 * @return string
	 */
	public function groupsToHtml() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.widgets');

		$str = Html::openTag('ul', array());

		foreach($this->_groups as $group) {
			$classPath = "";
			$criteriaBuilder = new CriteriaBuilder('tag');
			$criteriaBuilder->filterBy('groupId', $group->groupId);
			$criteriaBuilder->orderBy('tagTitle');
			$countTags = $criteriaBuilder->count();

			if($this->groupId == $group->groupId) {
				$classPath = 'path';
			}
			$str .= Html::tag('li',
				array(
					'id'=>'group-'.$group->groupId,
				),
				Html::link($group->groupTitle,
					array('group/', 'groupId' => $group->groupId),
					array('title'=>$group->groupTitle, 'class'=>$classPath)
				),
				false
			);
			if($countTags > 0) {
				$tags = $criteriaBuilder->findAll();
				$str .= Html::tag('ul', array(), false, false);
				foreach($tags as $tag) {
					$classPath = "";
					if($this->tagId == $tag->tagId) {
						$classPath = 'path';
					}
					$str .= Html::tag('li',
						array(
							'id'=>'tag-'.$tag->tagId,
						),
						Html::link($tag->tagTitle,
							array('tag/', 'tagId' => $tag->tagId),
							array('title'=>$tag->tagTitle, 'class'=>$classPath)
						),
						false
					);
				}
				$str .= Html::closeTag('ul');
			}
			$str .= Html::closeTag('li');
		}
		$str .= Html::closeTag('ul');
		return $str;
	}
}
