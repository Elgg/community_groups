<?php
/**
 * Create a new topic for an off-topic posting
 *
 * This also inserts a warning about the off topic posting
 */

$guid = get_input('guid');
$title = get_input('title');

$reply = get_entity($guid);
$original_topic = $reply->getContainerEntity();
$user_guid = $reply->getOwnerGUID();
$group_guid = $original_topic->getContainerGUID();

$grouptopic = new ElggObject();
$grouptopic->subtype = "discussion";
$grouptopic->owner_guid = $user_guid;
$grouptopic->container_guid = $group_guid;
$grouptopic->access_id = $original_topic->access_id;
$grouptopic->title = $title;
$grouptopic->description = $reply->description;
$grouptopic->status = 'open';
if (!$grouptopic->save()) {
	register_error(elgg_echo('discussion:error:notsaved'));
	forward(REFERER);
}

elgg_delete_river(array('object_guid' => $reply->guid));

elgg_create_river_item(array(
	'view' => 'river/object/discussion/create',
	'action_type' => 'create',
	'subject_guid' => $user_guid,
	'object_guid' => $grouptopic->guid,
	'posted' => $reply->time_created
));

// Replace the original content with a note and add a link that takes to the new topic
$link = elgg_view('output/url', array(
	'href' => $grouptopic->getURL(),
	'text' => elgg_echo('cg:form:offtopic:warning:link'),
));
$warning_text = elgg_echo('cg:form:offtopic:warning');
$reply->description = "[{$warning_text} {$link}]";
$reply->save();

$user = get_user($user_guid);
$site = elgg_get_site_entity();

$subject = elgg_echo('cg:forum:offtopic:notify:title', array(), $user->language);
$message = elgg_echo('cg:forum:offtopic:notify:body', array(
	$user->name,
	$site->name,
	elgg_get_excerpt($grouptopic->description, 80),
	$grouptopic->getURL(),
), $user->language);

// Let the user know that the comment was moved
notify_user($user_guid, $site->guid, $subject, $message,
	array(
		'action' => 'move',
		'object' => $grouptopic,
	)
);

system_message(elgg_echo('cg:forum:offtopic:success'));
forward($grouptopic->getURL());
