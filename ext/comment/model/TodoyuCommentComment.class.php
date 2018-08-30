<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Task comment object
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentComment extends TodoyuBaseObject {

	/**
	 * Initialize comment
	 *
	 * @param	Integer		$idComment		Comment ID
	 */
	public function __construct($idComment) {
		$idComment	= intval($idComment);

		parent::__construct($idComment, 'ext_comment_comment');
	}



	/**
	 * Get comment text
	 *
	 * @return	String
	 */
	public function getComment() {
		return $this->get('comment');
	}



	/**
	 * Get comment text for quoting
	 * Text is prefixed with >
	 *
	 * @return	String
	 */
	public function getCommentQuotedText() {
		return '<p></p>' . TodoyuCommentCommentManager::getPrefixedResponseLines($this->getComment());
	}



	/**
	 * Get ID of the task the comment is added to
	 *
	 * @return	Integer
	 */
	public function getTaskID() {
		return intval($this->data['id_task']);
	}



	/**
	 * Get task the comment is added to
	 *
	 * @return	TodoyuProjectTask
	 */
	public function getTask() {
		return TodoyuProjectTaskManager::getTask($this->getTaskID());
	}



	/**
	 * Get ID of the project of the task the comment is added to
	 *
	 * @return	Integer
	 */
	public function getProjectID() {
		return $this->getTask()->getProjectID();
	}



	/**
	 * Get project of the task the comment is added to
	 *
	 * @return	TodoyuProjectProject
	 */
	public function getProject() {
		return $this->getTask()->getProject();
	}



	/**
	 * Get comment label
	 *
	 * @param	Boolean		$withTaskTitle
	 * @param	Boolean		$fullTaskTitle
	 * @return	String
	 */
	public function getLabel($withTaskTitle = true, $fullTaskTitle = false) {
		$label	= 'C' . $this->getID();

		if( $withTaskTitle ) {
			$taskTitle	= $this->getTask()->getLabel($fullTaskTitle);
			if( $fullTaskTitle ) {
				$taskTitle .= ', ' . $this->getTask()->getTaskNumber(true);
			}
			$label		= $label . ' (' . $taskTitle . ')';
		}

		return $label;
	}



	/**
	 * Get persons being stored to have a feedback requested from to this comment
	 *
	 * @param	Mixed	[$isSeen]
	 * @return	Array[]
	 */
	public function getFeedbackPersonsData($isSeen = null) {
		return TodoyuCommentFeedbackManager::getFeedbackPersons($this->getID(), $isSeen);
	}



	/**
	 * Get IDs of feedback persons
	 *
	 * @param	Mixed		[$isSeen]
	 * @return	Integer[]
	 */
	public function getFeedbackPersonsIDs($isSeen = null) {
		$feedbackPersonsData	= $this->getFeedbackPersonsData($isSeen);

		return TodoyuArray::getColumn($feedbackPersonsData, 'id');
	}



	/**
	 * Get infos about involved persons
	 * - Feedback
	 * - Email
	 *
	 * @return	Array[]
	 */
	public function getInvolvedPersonInfos() {
		$feedbackPersonsData	= $this->getFeedbackPersonsData();
		$emailReceiversData		= $this->getEmailReceiversData();
		$involvedPersonInfos	= array();

			// Add feedback persons
		foreach($feedbackPersonsData as $feedbackPersonData) {
			$feedbackSeen	= intval($feedbackPersonData['is_seen']) === 1;
			$data	= array(
				'feedback'		=> true,
				'feedbackSeen'	=> $feedbackSeen,
				'personID'		=> $feedbackPersonData['id'],
				'fullname'		=> $feedbackPersonData['lastname'] . ' ' . $feedbackPersonData['firstname'],
				'is_dummy'		=> $feedbackPersonData['is_dummy'],
				'class'			=> $feedbackSeen ? 'commentperson-approved' : 'commentperson-unapproved',
				'key'			=> $feedbackPersonData['id']
			);

			$tuple	= 'contactperson:' . $feedbackPersonData['id'];

				// If feedback person also received an email, combine it
			if( isset($emailReceiversData[$tuple]) ) {
				$data['email'] = true;
				unset($emailReceiversData[$tuple]);
			}

			$involvedPersonInfos[] = $data;
		}

			// Add all email receivers
		foreach($emailReceiversData as $receiverTuple => $receiverData) {
			$data = array(
				'email'	=> true
			);

			if( substr($receiverTuple, 0, 13) === 'contactperson' ) {
				$idPerson			= intval(substr($receiverTuple, 14));
				$data['personID']	= $idPerson;
				$data['key']		= $idPerson;
			} else {
				$data['receiverTuple']	= $receiverTuple;
				$data['key']			= $receiverTuple;
			}

			$involvedPersonInfos[] = $data;
		}

		return $involvedPersonInfos;
	}



	/**
	 * Get mail persons
	 *
	 * @return	Array[]
	 */
	public function getEmailReceiversData() {
		$mailReceivers	= TodoyuCommentMailManager::getEmailReceivers($this->getID());

		$data	= array();
		foreach($mailReceivers as $mailReceiver) {
			$data[$mailReceiver->getTuple()] = $mailReceiver->getData();
		}

		return $data;
	}



//	/**
//	 * Get IDs of email persons
//	 *
//	 * @return	Integer
//	 */
//	public function getEmailReceiversIDs() {
//		$emailPersonsData	= $this->getEmailReceiversData();
//
//		return TodoyuArray::getColumn($emailPersonsData, 'id');
//	}


	/**
	 * Check whether comment has open feedbacks from persons not employed by an internal company
	 *
	 * @return	Boolean
	 */
	public function hasOpenFeedbacksFromExternals() {
		$personIDsFeedback	= $this->getFeedbackPersonsIDs(false);

			// Check all persons with open feedbacks to not (also) belong to an internal company
		foreach( $personIDsFeedback as $idPerson ) {
			$companies	= TodoyuContactPersonManager::getPerson($idPerson)->getCompanies();
			$isInternal	= false;
			foreach($companies as $company) {
				if( $company->isInternal() ) {
					$isInternal	= true;
				}
			}
			if( $isInternal === false ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Get update person ID
	 *
	 * @return	Integer
	 */
	public function getPersonUpdateID() {
		return $this->getInt('id_person_update');
	}



	/**
	 * Get update person
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPersonUpdate() {
		return TodoyuContactPersonManager::getPerson($this->getPersonUpdateID());
	}



	/**
	 * Check whether comment has an update person
	 *
	 * @return	Boolean
	 */
	public function hasPersonUpdate() {
		return $this->getPersonUpdateID() !== 0;
	}



	/**
	 * Check if comment is locked because of its task
	 *
	 * @return	Boolean
	 */
	public function isLocked() {
		return TodoyuProjectTaskManager::isLocked($this->getTaskID());
	}



	/**
	 * @return	Boolean
	 */
	public function isPublic() {
		return intval($this->data['is_public']) === 1;
	}



	/**
	 * Check whether current person can delete this comment
	 *
	 * @return	Boolean
	 */
	public function canCurrentPersonDelete() {
		$deleteAll	= Todoyu::allowed('comment', 'comment:deleteAll');
		$deleteOwn	= Todoyu::allowed('comment','comment:deleteOwn') && $this->isCurrentPersonCreator();

		return (! $this->isLocked()) && ($deleteAll || $deleteOwn);
	}



	/**
	 * Check whether the current user can edit the comment
	 *
	 * @return	Boolean
	 */
	public function canCurrentPersonEdit() {
		$editAll	= Todoyu::allowed('comment', 'comment:editAll');
		$editOwn	= Todoyu::allowed('comment','comment:editOwn') && $this->isCurrentPersonCreator();

		return !$this->isLocked() && $editAll || $editOwn;
	}



	/**
	 * Check whether the current user can make the comment public
	 *
	 * @return	Boolean
	 */
	public function canCurrentPersonMakePublic() {
		return $this->canCurrentPersonEdit();
	}



	/**
	 * Get label for update info
	 *
	 * @return	String|Boolean
	 */
	public function getUpdateInfoLabel() {
		$label	= false;

		if( $this->hasPersonUpdate() ) {
			$data	= array(
				$this->getPersonUpdate()->getFullName(),
				TodoyuTime::format($this->getDateUpdate(), 'datetime')
			);
			$label	= TodoyuLabelManager::getFormatLabel('comment.ext.updateInfo', $data);
		}

		return $label;
	}



	/**
	 * Get warning label if problems with a comments occur
	 * Problems:
	 * - Feedback is requested from an external person
	 * but:
	 * - Task is not public
	 * - Comment is not public
	 *
	 * @return	String|Boolean
	 */
	public function getPublicFeedbackWarning() {
		$label	= false;

		if( TodoyuAuth::isInternal() ) {
			if( $this->hasOpenFeedbacksFromExternals() ) {
				if( !$this->getTask()->isPublic() ) {
					$label	= Todoyu::Label('comment.ext.publicFeedbackWarning.task');
				} elseif( !$this->isPublic() ) {
					$label	= Todoyu::Label('comment.ext.publicFeedbackWarning.comment');
				}
			}
		}

		return $label;
	}


	/**
	 * Get actions
	 *
	 * @return	Array[]
	 */
	protected function getActions() {
		$actions	= array();

		if(  $this->canCurrentPersonEdit() ) {
			$actions['edit'] = array(
				'id'		=> 'edit',
				'onclick'	=> 'Todoyu.Ext.comment.Edit.editComment(' . $this->getTaskID() . ', ' . $this->getID() . ')',
				'class'		=> 'edit',
				'label'		=> 'comment.ext.icon.edit',
				'position'	=> 10
			);
		}

		if( $this->canCurrentPersonMakePublic() ) {
			$actions['makePublic'] = array(
				'id'		=> 'makePublic',
				'onclick'	=> 'Todoyu.Ext.comment.Comment.togglePublic(' . $this->getID() . ')',
				'class'		=> 'makePublic',
				'label'		=> 'comment.ext.icon.toggleCustomerVisibility',
				'position'	=> 20
			);

			if( $this->isPublic() ) {
				$actions['makePublic']['class'] .= ' isPublic';
			}
		}

		if( $this->canCurrentPersonDelete() ) {
			$actions['remove'] = array(
				'id'		=> 'remove',
				'onclick'	=> 'Todoyu.Ext.comment.Comment.remove(' . $this->getID() . ')',
				'class'		=> 'remove',
				'label'		=> 'comment.ext.icon.remove',
				'position'	=> 30
			);
		}

			// Quote comment
		$actions['quote'] = array(
			'id'		=> 'quote',
			'onclick'	=> 'Todoyu.Ext.comment.Comment.quote(' . $this->getTaskID() . ', ' . $this->getID() . ')',
			'class'		=> 'quote',
			'label'		=> 'comment.ext.quote',
			'position'	=> 40
		);
			// Mail reply
		$actions['mailReply'] = array(
			'id'		=> 'mailreply',
			'label'		=> 'comment.ext.mailReply',
			'onclick'	=> 'Todoyu.Ext.comment.Comment.mailReply(' . $this->getTaskID() . ', ' . $this->getID() . ')',
			'class'		=> 'mailReply',
			'position'	=> 45
		);

		$actions	= TodoyuHookManager::callHookDataModifier('comment', 'comment.actions', $actions, array($this->getID(), $this->getTaskID(), $this));
		$actions	= TodoyuArray::sortByLabel($actions, 'position');

		return $actions;
	}



	/**
	 * Get additional content items data
	 *
	 * @return	Array[]
	 */
	protected function getAdditionalContentItems() {
		return TodoyuHookManager::callHookDataModifier('comment', 'comment.additionalContentItems', array(), array($this->getID()));
	}



	/**
	 * Get assets which are attached
	 *
	 * @return	TodoyuAssetsAsset[]
	 */
	public function getAssets() {
		$assetIDs	= $this->getAssetsIDs();

		return TodoyuRecordManager::getRecordList('TodoyuAssetsAsset', $assetIDs);
	}



	/**
	 * Get IDs of attached assets
	 *
	 * @return	Integer[]
	 */
	public function getAssetsIDs() {
		return TodoyuCommentAssetManager::getAssetIDs($this->getID());
	}



	/**
	 * Check whether assets are attached
	 *
	 * @return	Boolean
	 */
	public function hasAssets() {
		return sizeof($this->getAssetsIDs()) > 0;
	}



	/**
	 * Get template data for all the assets
	 *
	 * @return	Array[]
	 */
	protected function getAssetsTemplateData() {
		$assets	= $this->getAssets();
		$data	= array();

		foreach($assets as $asset) {
			$data[$asset->getID()] = $asset->getTemplateData();
		}

		return $data;
	}



	/**
	 * Load comment foreign data: creator, feedback persons, approval state
	 *
	 * @param	Boolean		$loadRenderData
	 */
	protected function loadForeignData($loadRenderData = false) {
		$idComment	= $this->getID();

			// Basic foreign data
		if( !$this->has('person_create') ) {
			$this->data['person_create']	= $this->getPersonCreate()->getTemplateData(false);
			$this->data['involvedPersons']	= $this->getInvolvedPersonInfos();
			$this->data['locked']			= $this->isLocked();
			$this->data['assets']			= $this->getAssetsIDs();
			$this->data['feedback']			= $this->getFeedbackPersonsIDs();
		}

			// Extra data which is only required for detail rendering
		if( $loadRenderData && !$this->has('isUnapproved') ) {
			$feedbackPersonsData= TodoyuCommentFeedbackManager::getFeedbackPersons($idComment);
			$feedbackPersonIDs	= array_keys($feedbackPersonsData);
			$this->data['isFeedbackPerson']			= in_array(Todoyu::personid(), $feedbackPersonIDs);
			$this->data['isUnapproved']				= TodoyuCommentFeedbackManager::isCommentUnseen($idComment);
			$this->data['canDelete']				= $this->canCurrentPersonDelete();
			$this->data['canEdit']					= $this->canCurrentPersonEdit();
			$this->data['canMakePublic']			= $this->canCurrentPersonMakePublic();
			$this->data['updateInfo']				= $this->getUpdateInfoLabel();
			$this->data['publicFeedbackWarning']	= $this->getPublicFeedbackWarning();
			$this->data['actions']					= $this->getActions();
			$this->data['additionalContentItems']	= $this->getAdditionalContentItems();
			$this->data['assetInfos']				= $this->getAssetsTemplateData();
		}
	}



	/**
	 * Prepare comments rendering template data (creation person, having been seen status, feedback persons)
	 *
	 * @param	Boolean		$loadForeignData
	 * @param	Boolean		$loadRenderData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false, $loadRenderData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData($loadRenderData);
		}

		return parent::getTemplateData();
	}

}

?>