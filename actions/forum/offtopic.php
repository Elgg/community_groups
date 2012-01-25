<?php
/**
 * Create a new topic for an off-topic posting
 *
 * This also inserts a warning about the off topic posting
 */

$user_guid = get_input('user_guid');
$group_guid = get_input('group_guid');
$post_id = get_input('post_id');
$title = get_input('title');
$topic_guid = get_input('topic_guid');

$post = get_annotation($post_id);
$original_topic = get_entity($post->entity_guid);

$new_text = $post->value;
$new_text .= '<p>[' . elgg_echo('cg:form:offtopic:warning') . ']</p>';

// create a new post
if ($topic_guid == 0) {
	$grouptopic = new ElggObject();
	$grouptopic->subtype = "groupforumtopic";
	$grouptopic->owner_guid = $user_guid;
	$grouptopic->container_guid = $group_guid;
	$grouptopic->access_id = $original_topic->access_id;
	$grouptopic->title = $title;
	$grouptopic->status = 'open';
	if (!$grouptopic->save()) {
		register_error(elgg_echo("grouptopic:error"));
		forward(REFERER);
	}

	remove_from_river_by_annotation($post->id);
	add_to_river('river/forum/topic/create', 'create', $user_guid, $grouptopic->guid,
			$grouptopic->access_id, $original_topic->getTimeCreated());

	update_annotation($post->id, 'group_topic_post', $new_text, $post->value_type, $post->owner_guid, $post->access_id);
	$result = update_data("UPDATE {$CONFIG->dbprefix}annotations set entity_guid=$grouptopic->guid where id=$post->id");

	system_message(elgg_echo('cg:forum:offtopic:success'));
	forward($grouptopic->getURL());
} else {
	// move annotation to another topic
	$topic_guid = sanitise_int($topic_guid);
	$grouptopic = get_entity($topic_guid);
	if (!$grouptopic instanceof ElggObject || $grouptopic->getSubtype() != 'groupforumtopic') {
		register_error(elgg_echo("grouptopic:error"));
		forward(REFERER);
	}
	update_annotation($post->id, 'group_topic_post', $new_text, $post->value_type, $post->owner_guid, $post->access_id);
	$result = update_data("UPDATE {$CONFIG->dbprefix}annotations set entity_guid=$topic_guid where id=$post->id");
	forward($grouptopic->getURL());
}
