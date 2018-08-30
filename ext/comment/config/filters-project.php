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
 * Unseen feedback requests
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['unseenFeedback'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_unseenFeedback',
	'label'		=> 'comment.filter.unseenFeedback',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'checkbox',
	'wConf'		=> array(
		'checked'	=> true
	)
);

/**
 * Unseen comment feedback request for person
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['unseenFeedbackPerson'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_unseenFeedbackPerson',
	'label'		=> 'comment.filter.unseenFeedbackPerson',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'text',
	'internal'	=> true,
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
		'negation'		=> array(
			'labelTrue'	=> 'comment.filter.isSeen.negation.true',
			'labelFalse'=> 'comment.filter.isSeen.negation.false'
		)
	)
);

Todoyu::$CONFIG['FILTERS']['TASK']['filters']['unseenFeedbackCurrentPerson'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_unseenFeedbackCurrentPerson'
);

/**
 * Feedback request by current user, adressee (optional) has/not seen the comment
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['commentMyFeedbackRequestPerson'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_commentMyFeedbackRequestPerson',
	'label'		=> 'comment.filter.project.commentMyFeedbackRequest',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'text',
	'internal'	=> true,
	'wConf'		=> array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
		'multiple'		=> true,
		'size'			=> 5,
		'negation'		=> array(
			'labelTrue'	=> 'comment.filter.isSeen.negation.true',
			'labelFalse'=> 'comment.filter.isSeen.negation.false'
		)
	)
);

Todoyu::$CONFIG['FILTERS']['TASK']['filters']['commentIsPublicForExternals'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_commentIsPublicForExternals'
);

/**
 * Unseen comment feedback request for roles
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['unseenFeedbackRoles'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_unseenFeedbackRoles',
	'label'		=> 'comment.filter.unseenFeedbackRoles',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'select',
	'internal'	=> true,
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuRoleDatasource::getRoleOptions'
	)
);

/**
 * Comment full-text search
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['commentFulltext'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_fulltext',
	'label'		=> 'comment.filter.commentFulltext',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'text',
	'wConf' => array(
		'autocomplete'	=> false,
		'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
		'negation'	=> 'default'
	)
);

/**
 * Comment author person
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['commentWrittenPerson'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_commentWrittenPerson',
	'label'		=> 'comment.filter.commentWrittenPerson',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'text',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
		'negation'		=> 'default'
	)
);

/**
 * Comment author roles
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['commentWrittenRoles'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_commentWrittenRoles',
	'label'		=> 'comment.filter.commentWrittenRoles',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'select',
	'internal'	=> true,
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuRoleDatasource::getRoleOptions'
	)
);

/**
 * Comment creation date
 */
Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['commentCreatedate'] = array(
	'funcRef'	=> 'TodoyuCommentTaskFilter::Filter_commentDateCreate',
	'label'		=> 'comment.filter.commentCreatedate',
	'optgroup'	=> 'comment.ext.search.label',
	'widget'	=> 'date',
	'wConf'		=> array(
		'negation'	=> 'datetime'
	)
);



/**
 * Comment sorting for tasks
 */

	// Last comment added, no comment = bottom
Todoyu::$CONFIG['FILTERS']['TASK']['sorting']['commentLastAdd'] = array(
	'label'		=> 'comment.filter.sorting.commentLastAdd',
	'optgroup'	=> 'comment.ext.search.label',
	'funcRef'	=> 'TodoyuCommentTaskFilter::Sorting_commentLastAdd',
	'require'	=> 'comment.comment:seeAll'
);

	// Last public comment added, no comment = top
Todoyu::$CONFIG['FILTERS']['TASK']['sorting']['commentLastAddPublic'] = array(
	'label'		=> 'comment.filter.sorting.commentLastAddPublic',
	'optgroup'	=> 'comment.ext.search.label',
	'funcRef'	=> 'TodoyuCommentTaskFilter::Sorting_commentLastAddPublic'
);

?>