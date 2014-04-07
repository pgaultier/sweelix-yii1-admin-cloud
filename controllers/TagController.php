<?php
/**
 * File TagController.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.cloud.controllers
 */

namespace sweelix\yii1\admin\cloud\controllers;
use sweelix\yii1\admin\core\web\Controller;
use sweelix\yii1\ext\db\CriteriaBuilder;
use sweelix\yii1\ext\entities\Node;
use sweelix\yii1\ext\entities\Content;
use sweelix\yii1\ext\entities\Tag;
use sweelix\yii1\ext\entities\NodeTag;
use sweelix\yii1\ext\entities\ContentTag;
use sweelix\yii1\admin\core\models\Node as FormNode;
use sweelix\yii1\admin\core\models\Content as FormContent;
use sweelix\yii1\admin\core\models\Tag as FormTag;
use sweelix\yii1\web\helpers\Html;

/**
 * Class TagController
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.cloud.controllers
 * @since     1.0.0
 *
 * @property mixed $templateConfig
 */
class TagController extends Controller {

	/**
	 * @var array breadcrumbs
	 */
	private $_breadCrumb=array();

	/**
	 * Lazy load and build breadcrumb for selected id.
	 *
	 * @param integer $currentGroupId target Group of the breadcrumb
	 * @param integer $tagId target Tag if the breadcrumb
	 *
	 * @return array
	 * @since  3.0.0
	*/
	public function buildBreadcrumb($tagId) {
		if(isset($this->_breadCrumb[$tagId]) === false) {
			$tag = Tag::model()->findByPk($tagId);
			$this->_breadCrumb[$tagId] = array(
				array(
					'content' => $tag->group->groupTitle,
					'url' => array('group/', 'groupId'=>$tag->group->groupId),
				),
				array(
					'content' => $tag->tagTitle,
					'url' => array('tag/', 'tagId'=>$tag->tagId),
				),
			);
		}
		return $this->_breadCrumb[$tagId];
	}

	/**
	 * Build main menu using positions (0 indexed)
	 * if mainOption or secondaryOption is set to false, thei part of the
	 * menu is not shown
	 *
	 * @param mixed $mainOption      index of selected option. false if main options should be hidden
	 * @param mixed $secondaryOption index of selected option. false if secondary options should be hidden
	 *
	 * @return array
	 * @since  3.0.0
	 */
	public function buildMainMenu($mainOption=null, $secondaryOption=null) {
		$mainMenu = array(
				'main' => array(
						array(
								'content' => \Yii::t('cloud', 'Tag Content'),
								'url' => array('tag/listContent', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('cloud', 'Tag Node'),
								'url' => array('tag/listNode', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('cloud', 'Tag Configuration'),
								'url' => array('tag/detail', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						)
				),
				'secondary' => array(
						array(
								'content' => \Yii::t('cloud', 'Tag detail'),
								'url' => array('tag/detail', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('cloud', 'Properties'),
								'url' => array('tag/property', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('cloud', 'Admin'),
								'url' => array('tag/admin', 'tagId'=>$this->currentTag->tagId),
								'active' => false,
						),
				)
		);
		if($mainOption === false) {
			unset($mainMenu['main']);
		} if($mainOption !== null) {
			$mainOption = \CPropertyValue::ensureInteger($mainOption);
			if(isset($mainMenu['main'][$mainOption]) === true) {
				unset($mainMenu['main'][$mainOption]['url']);
				$mainMenu['main'][$mainOption]['active'] = true;
			}
		}

		if($secondaryOption === false) {
			unset($mainMenu['secondary']);
		} if($secondaryOption !== null) {
			$secondaryOption = \CPropertyValue::ensureInteger($secondaryOption);
			if(isset($mainMenu['secondary'][$secondaryOption]) === true) {
				unset($mainMenu['secondary'][$secondaryOption]['url']);
				$mainMenu['secondary'][$secondaryOption]['active'] = true;
			}
		}
		return $mainMenu;
	}
	/**
	 * @var array list of tags in selected group
	 */
	private $_tagsInGroup;

	/**
	 * Adding asynchronous upload / delete actions
	 * @see Html::activeAsyncFileUpload
	 *
	 * @return array
	 * @since  1.2.0
	 */
	public function actions() {
		return array(
			'asyncUpload' => 'sweelix\yii1\web\actions\UploadFile',
			'asyncDelete' => 'sweelix\yii1\web\actions\DeleteFile',
			'asyncPreview' => 'sweelix\yii1\web\actions\PreviewFile',
		);
	}

	/**
	 * Default action. Should redirect to real action
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionIndex() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
		$this->redirect(array(
			'detail',
			'tagId'=>\Yii::app()->request->getParam('tagId', 0)
		));
	}

	/**
	 * Admin will be a placeholder for specific actions on content
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionAdmin() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$tag = new FormTag();
			$tag->targetTagId = $this->currentTag->tagId;
			$tag->selected = false;
			$this->render('admin', array(
				'mainMenu' => $this->buildMainMenu(2, 2),
				'breadcrumb' => $this->buildBreadcrumb($this->currentTag->tagId),
				'tag'=>$tag,
				'sourceTag'=> $this->currentTag
			));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}


	/**
	 * Start tag creation
	 *
	 * @return void
	 */
	public function actionNew() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			\Yii::app()->session['newtag'] = array();
			$this->redirect(array('step1', 'groupId'=>$this->currentGroup->groupId));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a tag
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionStep1() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$newTag = new Tag();
			$newTag->setScenario('createStep1');
			$newTag->attributes = \Yii::app()->session['newtag'];
			if(isset($_POST[Html::modelName($newTag)]) === true) {
				$newTag->attributes = $_POST[Html::modelName($newTag)];
				if($newTag->validate() === true) {
					\Yii::app()->session['newtag'] = $_POST[Html::modelName($newTag)];
					$this->redirect(array('step2', 'groupId'=>$this->currentGroup->groupId));
				}
			}
			$criteriaBuilder = new CriteriaBuilder('group');
			$criteriaBuilder->orderBy('groupTitle');
			$groups = $criteriaBuilder->findAll();

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'list');
			$criteriaBuilder->orderBy('templateTitle');
			$templates = $criteriaBuilder->findAll();
			if(\Yii::app()->getRequest()->isAjaxRequest === true) {
				$this->renderPartial('_property', array(
					'tag'=>$newTag,
					'templates'=>$templates,
					'groups'=>$groups,
				));
			} else {
				$this->render('step1', array(
					'group'=>$this->currentGroup,
					'tag'=>$newTag,
					'templates'=>$templates,
					'groups'=>$groups,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionStep2() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$newTag = new Tag();
			$newTag->setScenario('createStep2');
			$newTag->templateId = \Yii::app()->session['newtag']['templateId'];
			$newTag->reconfigure();
			$newTag->attributes = \Yii::app()->session['newtag'];

			if(isset($_POST[Html::modelName($newTag)]) === true) {
				$newTag->attributes = $_POST[Html::modelName($newTag)];
				$tagStatus = $newTag->validate();
				if($tagStatus === true) {
					if($newTag->save() === true) {
						$this->redirect(array('index', 'tagId'=>$newTag->tagId));
					}
				}
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('step2', array(
					'tag'=>$newTag,
				));
			} else {
				$this->renderPartial('_detail', array(
					'tag'=>$newTag,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}


	/**
	 * Change data properties of tag.
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDetail() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$notice = false;
			if(isset($_POST[Html::modelName($this->currentTag)]) === true) {
				$this->currentTag->setScenario('updateDetail');
				$this->currentTag->attributes = $_POST[Html::modelName($this->currentTag)];
				$tagStatus = $this->currentTag->validate();
				if($tagStatus === true) {
					if($this->currentTag->save() === true) {
						$this->currentTag->refresh();
					}
				}
				$notice = true;
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('detail', array(
					'mainMenu' => $this->buildMainMenu(2, 0),
					'breadcrumb' => $this->buildBreadcrumb($this->currentTag->tagId),
					'tag' => $this->currentTag,
					'notice' => $notice,
				));
			} else {
				$this->renderPartial('_detail', array(
					'tag' => $this->currentTag,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 * Change presentation properties of tag
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionProperty(){
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$notice = false;
			if(isset($_POST[Html::modelName($this->currentTag)]) === true) {
				$originalGroup = $this->currentTag->groupId;
				$this->currentTag->scenario = 'updateProperty';
				$this->currentTag->attributes = $_POST[Html::modelName($this->currentTag)];
				if($this->currentTag->validate() === true) {
					$this->currentTag->save();
					if($originalGroup !== $this->currentTag->groupId) {
						$this->redirect(array('property', 'tagId'=>$this->currentTag->tagId));
					}
				}
				$notice = true;
			}
			$criteriaBuilder = new CriteriaBuilder('group');
			$criteriaBuilder->orderBy('groupTitle');
			$groups = $criteriaBuilder->findAll();

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'list');
			$criteriaBuilder->orderBy('templateTitle');
			$templates = $criteriaBuilder->findAll();

			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_property', array(
					'tag'=>$this->currentTag,
					'templates'=>$templates,
					'groups'=>$groups,
					'notice' => $notice,
				));
			} else {
				$this->render('property', array(
					'mainMenu' => $this->buildMainMenu(2, 1),
					'breadcrumb' => $this->buildBreadcrumb($this->currentTag->tagId),
					'tag'=>$this->currentTag,
					'templates'=>$templates,
					'groups'=>$groups,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 * List nodes for selected tag
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionListNode($withoutButtons=false, $page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			if(isset($_POST[Html::modelName('sweelix\yii1\admin\core\models\Node')]) === true) {
				foreach($_POST[Html::modelName('sweelix\yii1\admin\core\models\Node')] as $formData) {
					$swfNode = new FormNode();
					$swfNode->scenario = 'updateTag';
					$swfNode->attributes = $formData;
					if($swfNode->validate() === true) {
						if(($this->currentTag->group->groupType === 'single') && ($swfNode->selected == true)) {
							foreach($this->getTagsInGroup() as $tagId) {
								NodeTag::model()->deleteByPk(array(
									'tagId'=>$tagId,
									'nodeId'=>$swfNode->nodeId
								));
							}
						} else {
							NodeTag::model()->deleteByPk(array(
								'tagId'=>$swfNode->tagId,
								'nodeId'=>$swfNode->nodeId
							));
						}
						if($swfNode->selected) {
							$tagNode = new NodeTag();
							$tagNode->tagId = $swfNode->tagId;
							$tagNode->nodeId = $swfNode->nodeId;
							$tagNode->save();
						}
					}
				}
			}
			$nodeCriteriaBuilder = new CriteriaBuilder('node');
			$nodeCriteriaBuilder->orderBy('nodeLeftId', 'asc');
			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_listNode', array(
					'tag'=>$this->currentTag,
					'nodesDataProvider' => $nodeCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
					'withoutButtons' => $withoutButtons
				));
			} else {
				$this->render('listNode', array(
					'mainMenu' => $this->buildMainMenu(1, false),
					'breadcrumb' => $this->buildBreadcrumb($this->currentTag->tagId),
					'tag'=>$this->currentTag,
					'nodesDataProvider' => $nodeCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 * List contents for selected tag
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionListContent($withoutButtons=false, $page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			if(isset($_POST[Html::modelName('sweelix\yii1\admin\core\models\Content')]) === true) {
				foreach($_POST[Html::modelName('sweelix\yii1\admin\core\models\Content')] as $formData) {
					$swfContent = new FormContent();
					$swfContent->scenario = 'updateTag';
					$swfContent->attributes = $formData;
					if($swfContent->validate() === true) {
						if(($this->currentTag->group->groupType === 'single') && ($swfContent->selected == true)) {
							foreach($this->getTagsInGroup() as $tagId) {
								ContentTag::model()->deleteByPk(array(
									'tagId'=>$tagId,
									'contentId'=>$swfContent->contentId
								));
							}
						} else {
							ContentTag::model()->deleteByPk(array(
								'tagId'=>$swfContent->tagId,
								'contentId'=>$swfContent->contentId
							));
						}
						if($swfContent->selected) {
							$tagContent = new ContentTag();
							$tagContent->tagId = $swfContent->tagId;
							$tagContent->contentId = $swfContent->contentId;
							$tagContent->save();
						}
					}
				}
			}
			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('nodeId', null, '!=');
			$contentCriteriaBuilder->orderBy('contentId', 'desc');
			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_listContent', array(
					'tag' => $this->currentTag,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
					'withoutButtons' => $withoutButtons,
				));
			} else {
				$this->render('listContent', array(
					'mainMenu' => $this->buildMainMenu(0, false),
					'breadcrumb' => $this->buildBreadcrumb($this->currentTag->tagId),
					'tag'=>$this->currentTag,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}
/**
 *
 * Update Content Tag relative to the active checkbox
 *
 * @param 	integer $tagId tag's id
 * @param 	integer $contentId content's id
 * @return 	void
 * @since  	3.0.0
 */
	public function actionUpdateContentTag($tagId, $contentId, $page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$checked = \CPropertyValue::ensureBoolean(\Yii::app()->getRequest()->getParam('checked', false));
			// update du tag tagId
			ContentTag::model()->deleteAll(array(
				'condition' => 'tagId = :tagId AND contentId = :contentId',
				'params' => array(':tagId' => $tagId, ':contentId' => $contentId )
			));
			if($checked === true) {
				$contentTag = new ContentTag();
				$contentTag->tagId = $tagId;
				$contentTag->contentId = $contentId;
				$contentTag->save();
			}
			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('nodeId', null, '!=');
			$contentCriteriaBuilder->orderBy('contentId', 'desc');

			$this->renderPartial('_listContent', array(
					'tag' => $this->currentTag,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
					'withoutButtons' => true,
			));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}

	/**
	 *
	 * Update Node Tag relative to the active checkbox
	 *
	 * @param 	integer $tagId tag's id
	 * @param 	integer $nodeId node's id
	 * @return 	void
	 * @since  	3.0.0
	 */
	public function actionUpdateNodeTag($tagId, $nodeId, $page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$checked = \CPropertyValue::ensureBoolean(\Yii::app()->getRequest()->getParam('checked', false));
			// update du tag tagId
			NodeTag::model()->deleteAll(array(
			'condition' => 'tagId = :tagId AND nodeId = :nodeId',
			'params' => array(':tagId' => $tagId, ':nodeId' => $nodeId)
			));
			if($checked === true) {
				$nodeTag = new NodeTag();
				$nodeTag->tagId = $tagId;
				$nodeTag->nodeId = $nodeId;
				$nodeTag->save();
			}
			$nodeCriteriaBuilder = new CriteriaBuilder('node');
			$nodeCriteriaBuilder->orderBy('nodeLeftId', 'asc');
			$this->renderPartial('_listNode', array(
					'tag' => $this->currentTag,
					'nodesDataProvider' => $nodeCriteriaBuilder->getActiveDataProvider(['pagination' => [
						'pageSize' => $this->module->pageSize,
						'currentPage' => $page,
					]]),
					'withoutButtons' => true,
			));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}
	/**
	 * Retrieve list of tags in current group
	 *
	 * @return array
	 * @since  1.2.0
	 */
	private function getTagsInGroup() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			if($this->_tagsInGroup === null) {
				$this->_tagsInGroup = array();
				$criteriaBuilder = new CriteriaBuilder('tag');
				$criteriaBuilder->filterBy('groupId', $this->currentTag->group->groupId);
				$tagsInGroup = $criteriaBuilder->findAll();
				foreach($tagsInGroup as $tag) {
					$this->_tagsInGroup[] = $tag->tagId;
				}
			}
			return $this->_tagsInGroup;
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}


	/**
	 * delete selected tag
	 *
	 * @return void
	 */
	public function actionDelete() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$redirectUrl = array('admin', 'tagId' => $this->currentTag->tagId);
			$tag = new FormTag();
			if(isset($_POST[Html::modelName($tag)]) === true) {
				$tag->scenario = 'deleteTag';
				$tag->attributes = $_POST[Html::modelName($tag)];
				if($tag->validate() === true) {
					$redirectUrl = array('group/index', 'groupId'=>$this->currentTag->group->groupId);
					$tag = Tag::model()->findByPk($tag->targetTagId);
					$tag->delete();
				}
			}
			$this->redirect($redirectUrl);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}
	/**
	 * Change status of current selected content and go back to list
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public function actionChangeContentStatus($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$content = Content::model()->findByPk(\Yii::app()->getRequest()->getParam('contentId', 0));
			$mode = \Yii::app()->getRequest()->getParam('mode', 'draft');
			if($content !== null) {
				$content->contentStatus = $mode;
				$content->save();
			}

			$this->actionListContent(true, $page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}
	/**
	 * Change status of current selected node and go back to list
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public function actionChangeNodeStatus($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.cloud.controllers');
			$node = Node::model()->findByPk(\Yii::app()->getRequest()->getParam('nodeId', 0));
			$mode = \Yii::app()->getRequest()->getParam('mode', 'draft');
			if($node !== null) {
				$node->nodeStatus = $mode;
				$node->save();
			}
			$this->actionListNode(true, $page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.cloud.controllers');
			throw $e;
		}
	}
	/**
	 * Define filtering rules
	 *
	 * @return array
	 */
	public function filters() {
		return array(
			'accessControl',
			array(
				'sweelix\yii1\admin\core\filters\ContextTag - new, step1, step2, asyncUpload, asyncDelete, asyncPreview'
			),
			array(
				'sweelix\yii1\admin\core\filters\ContextGroup + new, step1, step2'
			),
			//'ajaxOnly + updateContentTag',
		);
	}

	/**
	 * Define access rules / rbac stuff
	 *
	 * @return array
	 */
	public function accessRules() {
		return array(
			array(
				'allow', 'roles' => array($this->getModule()->getName())
			),
			array(
				'deny', 'users'=>array('*'),
			),
		);
	}
}
