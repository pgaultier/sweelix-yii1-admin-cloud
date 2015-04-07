<?php
/**
 * File GroupController.php
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
use sweelix\yii1\ext\entities\Group;
use sweelix\yii1\ext\entities\NodeTag;
use sweelix\yii1\ext\entities\ContentTag;
use sweelix\yii1\admin\core\models\Group as FormGroup;
use sweelix\yii1\web\helpers\Html;
use CLogger;
use CPropertyValue;
use Exception;
use Yii;

/**
 * Class GroupController
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
class GroupController extends Controller
{

    /**
     * @var array breadcrumbs
     */
    private $breadCrumb = array();

    /**
     * Lazy load and build breadcrumb for selected id.
     *
     * @param integer $currentGroupId target Group of the breadcrumb
     *
     * @return array
     * @since  3.0.0
     */
    public function buildBreadcrumb($currentGroupId)
    {
        if (isset($this->breadCrumb[$currentGroupId]) === false) {
            $group = Group::model()->findByPk($currentGroupId);
            $this->breadCrumb[$currentGroupId] = array(
                array(
                    'url' => array('group/', 'groupId' => $group->groupId),
                    'content' => $group->groupTitle,
                ),
            );
        }
        return $this->breadCrumb[$currentGroupId];
    }

    /**
     * Build main menu using positions (0 indexed)
     * if mainOption or secondaryOption is set to false, thei part of the
     * menu is not shown
     *
     * @param mixed $mainOption index of selected option. false if main options should be hidden
     * @param mixed $secondaryOption index of selected option. false if secondary options should be hidden
     *
     * @return array
     * @since  3.0.0
     */
    public function buildMainMenu($mainOption = null, $secondaryOption = null)
    {
        $mainMenu = array(
            'main' => array(
                array(
                    'content' => Yii::t('cloud', 'Group Content'),
                    'url' => array('group/listContent', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                ),
                array(
                    'content' => Yii::t('cloud', 'Group Node'),
                    'url' => array('group/listNode', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                ),
                array(
                    'content' => Yii::t('cloud', 'Group Configuration'),
                    'url' => array('group/detail', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                )
            ),
            'secondary' => array(
                array(
                    'content' => Yii::t('cloud', 'Group detail'),
                    'url' => array('group/detail', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                ),
                array(
                    'content' => Yii::t('cloud', 'Properties'),
                    'url' => array('group/property', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                ),
                array(
                    'content' => Yii::t('cloud', 'Admin'),
                    'url' => array('group/admin', 'groupId' => $this->currentGroup->groupId),
                    'active' => false,
                ),
            )
        );
        if ($mainOption === false) {
            unset($mainMenu['main']);
        }
        if ($mainOption !== null) {
            $mainOption = CPropertyValue::ensureInteger($mainOption);
            if (isset($mainMenu['main'][$mainOption]) === true) {
                unset($mainMenu['main'][$mainOption]['url']);
                $mainMenu['main'][$mainOption]['active'] = true;
            }
        }

        if ($secondaryOption === false) {
            unset($mainMenu['secondary']);
        }
        if ($secondaryOption !== null) {
            $secondaryOption = CPropertyValue::ensureInteger($secondaryOption);
            if (isset($mainMenu['secondary'][$secondaryOption]) === true) {
                unset($mainMenu['secondary'][$secondaryOption]['url']);
                $mainMenu['secondary'][$secondaryOption]['active'] = true;
            }
        }
        return $mainMenu;
    }

    /**
     * @var array list of tags in selected group
     */
    private $tagsInGroup;

    /**
     * Adding asynchronous upload / delete actions
     * @see Html::activeAsyncFileUpload
     *
     * @return array
     * @since  1.2.0
     */
    public function actions()
    {
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
     */
    public function actionIndex()
    {
        Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
        $this->redirect(array(
            'detail',
            'groupId' => Yii::app()->request->getParam('groupId', 0)
        ));
    }

    /**
     * Admin will be a placeholder for specific actions on group
     *
     * @return void
     * @since  1.2.0
     */
    public function actionAdmin()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $group = new FormGroup();
            $group->targetGroupId = $this->currentGroup->groupId;
            $group->selected = false;
            $this->render('admin', array(
                'breadcrumb' => $this->buildBreadcrumb($this->currentGroup->groupId),
                'mainMenu' => $this->buildMainMenu(2, 2),
                'group' => $group,
                'sourceGroup' => $this->currentGroup,
            ));
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Start tag creation
     *
     * @return void
     * @since  1.2.0
     */
    public function actionNew()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            Yii::app()->session['newgroup'] = array();
            $this->redirect(array('step1'));
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Collect base information to create a group
     *
     * @return void
     * @since  1.2.0
     */
    public function actionStep1()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $newGroup = new Group();
            $newGroup->setScenario('createStep1');
            $newGroup->attributes = Yii::app()->session['newgroup'];
            if (isset($_POST[Html::modelName($newGroup)]) === true) {
                $newGroup->attributes = $_POST[Html::modelName($newGroup)];
                if ($newGroup->validate() === true) {
                    Yii::app()->session['newgroup'] = $_POST[Html::modelName($newGroup)];
                    $this->redirect(array('step2'));
                }
            }
            $criteriaBuilder = new CriteriaBuilder('template');
            $criteriaBuilder->filterBy('templateType', 'list');
            $criteriaBuilder->orderBy('templateTitle');
            $templates = $criteriaBuilder->findAll();
            if (Yii::app()->getRequest()->isAjaxRequest === false) {
                $this->render('step1', array(
                    'group' => $newGroup,
                    'sourceNode' => $this->currentNode,
                    'templates' => $templates,
                ));
            } else {
                $this->renderPartial('_property', array(
                    'group' => $newGroup,
                    'templates' => $templates,
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Collect base information to create a node
     *
     * @return void
     * @since  1.2.0
     */
    public function actionStep2()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $newGroup = new Group();
            $newGroup->setScenario('createStep2');
            $newGroup->templateId = Yii::app()->session['newgroup']['templateId'];
            $newGroup->reconfigure();
            $newGroup->attributes = Yii::app()->session['newgroup'];


            if (isset($_POST[Html::modelName($newGroup)]) === true) {
                $newGroup->attributes = $_POST[Html::modelName($newGroup)];
                $groupStatus = $newGroup->validate();
                if ($groupStatus === true) {
                    if ($newGroup->save() === true) {
                        $newGroup->save();
                        $this->redirect(array('index', 'groupId' => $newGroup->groupId));
                    }
                }
            }
            if (Yii::app()->getRequest()->isAjaxRequest === false) {
                $this->render('step2', array(
                    'group' => $newGroup,
                    'sourceNode' => $this->currentNode,
                ));
            } else {
                $this->renderPartial('_detail', array(
                    'group' => $newGroup,
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Change data properties of group.
     *
     * @return void
     * @since  1.2.0
     */
    public function actionDetail()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $notice = false;
            if (isset($_POST[Html::modelName($this->currentGroup)]) === true) {
                $this->currentGroup->setScenario('updateDetail');
                $this->currentGroup->attributes = $_POST[Html::modelName($this->currentGroup)];
                $groupStatus = $this->currentGroup->validate();
                if ($groupStatus === true) {
                    if ($this->currentGroup->save() === true) {
                        $this->currentGroup->refresh();
                    }
                }
                $notice = true;
            }
            if (Yii::app()->getRequest()->isAjaxRequest === false) {
                $this->render('detail', array(
                    'breadcrumb' => $this->buildBreadcrumb($this->currentGroup->groupId),
                    'mainMenu' => $this->buildMainMenu(2, 0),
                    'group' => $this->currentGroup,
                    'notice' => $notice,
                ));
            } else {
                $this->renderPartial('_detail', array(
                    'group' => $this->currentGroup,
                    'notice' => $notice,
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Change presentation properties of group
     *
     * @return void
     * @since  1.2.0
     */
    public function actionProperty()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $notice = false;
            if (isset($_POST[Html::modelName($this->currentGroup)]) === true) {
                $this->currentGroup->setScenario('updateProperty');
                $previousGroupType = $this->currentGroup->groupType;
                $this->currentGroup->attributes = $_POST[Html::modelName($this->currentGroup)];
                if ($this->currentGroup->validate() === true) {
                    if (($this->currentGroup->groupType === 'single')
                        && ($this->currentGroup->groupType != $previousGroupType)
                    ) {
                        foreach ($this->getTagsInGroup() as $tagId) {
                            ContentTag::model()->deleteAll(array(
                                'condition' => 'tagId = :tagId',
                                'params' => array(':tagId' => $tagId)
                            ));
                            NodeTag::model()->deleteAll(array(
                                'condition' => 'tagId = :tagId',
                                'params' => array(':tagId' => $tagId)
                            ));
                        }
                    }
                    $this->currentGroup->save();
                }
                $notice = true;
            }

            $criteriaBuilder = new CriteriaBuilder('template');

            $criteriaBuilder->filterBy('templateType', 'list');
            $criteriaBuilder->orderBy('templateTitle');
            $templates = $criteriaBuilder->findAll();
            if (Yii::app()->request->isAjaxRequest === true) {
                $this->renderPartial('_property', array(
                    'group' => $this->currentGroup,
                    'templates' => $templates,
                    'notice' => $notice,
                ));
            } else {
                $this->render('property', array(
                    'breadcrumb' => $this->buildBreadcrumb($this->currentGroup->groupId),
                    'mainMenu' => $this->buildMainMenu(2, 1),
                    'group' => $this->currentGroup,
                    'templates' => $templates,
                    'notice' => $notice,
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * List nodes for selected group
     *
     * @return void
     * @since  1.2.0
     */
    public function actionListNode($page = 0)
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $contentCriteriaBuilder = new CriteriaBuilder('node');
            $contentCriteriaBuilder->filterBy('nodeId', null, '!=');
            $contentCriteriaBuilder->orderBy('nodeId', 'asc');


            if (Yii::app()->request->isAjaxRequest === true) {
                $this->renderPartial('_listNode', array(
                    'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array(
                        'pagination' => array(
                            'pageSize' => $this->module->pageSize,
                            'currentPage' => $page,
                        )
                    )),
                    'group' => $this->currentGroup,
                ));
            } else {
                $this->render('listNode', array(
                    'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array(
                        'pagination' => array(
                            'pageSize' => $this->module->pageSize,
                            'currentPage' => $page,
                        )
                    )),
                    'breadcrumb' => $this->buildBreadcrumb($this->currentGroup->groupId),
                    'mainMenu' => $this->buildMainMenu(1, false),
                    'group' => $this->currentGroup,
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * List contents for selected group
     *
     * @return void
     */
    public function actionListContent($page = 0)
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $contentCriteriaBuilder = new CriteriaBuilder('content');
            $contentCriteriaBuilder->filterBy('nodeId', null, '!=');
            $contentCriteriaBuilder->orderBy('contentId', 'asc');
            if (Yii::app()->request->isAjaxRequest === true) {
                $this->renderPartial('_listContent', array(
                    'group' => $this->currentGroup,
                    'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array(
                        'pagination' => array(
                            'pageSize' => $this->module->pageSize,
                            'currentPage' => $page,
                        )
                    )),
                ));
            } else {
                $this->render('listContent', array(
                    'breadcrumb' => $this->buildBreadcrumb($this->currentGroup->groupId),
                    'mainMenu' => $this->buildMainMenu(0, false),
                    'group' => $this->currentGroup,
                    'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array(
                        'pagination' => array(
                            'pageSize' => $this->module->pageSize,
                            'currentPage' => $page,
                        )
                    )),
                ));
            }
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Retrieve list of tags in current group
     *
     * @return array
     */
    private function getTagsInGroup()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            if ($this->tagsInGroup === null) {
                $this->tagsInGroup = array();
                $criteriaBuilder = new CriteriaBuilder('tag');
                $criteriaBuilder->filterBy('groupId', $this->currentGroup->groupId);
                $tagsInGroup = $criteriaBuilder->findAll();
                foreach ($tagsInGroup as $tag) {
                    $this->tagsInGroup[] = $tag->tagId;
                }
            }
            return $this->tagsInGroup;
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * delete selected group
     *
     * @return void
     */
    public function actionDelete()
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $redirectUrl = array('admin', 'groupId' => $this->currentGroup->groupId);
            $group = new FormGroup();
            if (isset($_POST[Html::modelName($group)]) === true) {
                $group->scenario = 'deleteGroup';
                $group->attributes = $_POST[Html::modelName($group)];
                if ($group->validate() === true) {
                    $redirectUrl = array('group/index');
                    $group = Group::model()->findbyPk($group->targetGroupId);
                    $group->delete();
                }
            }
            $this->redirect($redirectUrl);
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Change status of current selected content and go back to list
     *
     * @return void
     * @since  3.0.0
     */
    public function actionChangeContentStatus($page = 0)
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $content = Content::model()->findByPk(Yii::app()->getRequest()->getParam('contentId', 0));
            $mode = Yii::app()->getRequest()->getParam('mode', 'draft');
            if ($content !== null) {
                $content->contentStatus = $mode;
                $content->save();
            }

            $this->actionListContent($page);
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Change status of current selected node and go back to list
     *
     * @return void
     * @since  3.0.0
     */
    public function actionChangeNodeStatus($page = 0)
    {
        try {
            Yii::trace(__METHOD__ . '()', 'sweelix.yii1.admin.cloud.controllers');
            $node = Node::model()->findByPk(Yii::app()->getRequest()->getParam('nodeId', 0));
            $mode = Yii::app()->getRequest()->getParam('mode', 'draft');
            if ($node !== null) {
                $node->nodeStatus = $mode;
                $node->save();
            }
            $this->actionListNode($page);
        } catch (Exception $e) {
            Yii::log(
                'Error in ' . __METHOD__ . '():' . $e->getMessage(),
                CLogger::LEVEL_ERROR,
                'sweelix.yii1.admin.cloud.controllers'
            );
            throw $e;
        }
    }

    /**
     * Define filtering rules
     *
     * @return array
     */
    public function filters()
    {
        return array(
            'accessControl',
            array(
                'sweelix\yii1\admin\core\filters\ContextGroup - new, step1, step2, asyncUpload, asyncDelete, asyncPreview'
            )
        );
    }

    /**
     * Define access rules / rbac stuff
     *
     * @return array
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'roles' => array($this->getModule()->getName())
            ),
            array(
                'deny',
                'users' => array('*'),
            ),
        );
    }
}
