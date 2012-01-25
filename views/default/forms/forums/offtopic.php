<?php
/**
 * Form for moving an off-topic post to a new topic
 */

$post = get_annotation($vars['post_id']);
$topics = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'groupforumtopic',
	'container_guid' => $vars['group_guid'],
	'wheres' => "e.guid != '$post->entity_guid'",
	'limit' => 0
));

$options = array(
	0 => '(' . elgg_echo('cg:forum:offtopic:new_post') . ')'
);

foreach ($topics as $topic) {
	$options[$topic->guid] = $topic->title;
}

$topics_dropdown = elgg_view('input/pulldown', array(
	'options_values' => $options,
	'internalname' => 'topic_guid',
));

echo '<div class="contentWrapper">';

echo '<p>';
echo '<label>' . elgg_echo('cg:forum:offtopic:move_to') . ' ' . $topics_dropdown;
echo '</label>';

echo '<p id="new-topic-title">';
echo '<label>' . elgg_echo('title') . '</label>';
echo elgg_view('input/text', array(
	'internalname' => 'title',
));
echo '</p>';

echo '<div>';
echo '<label>' . elgg_echo('cg:forum:offtopic:text') . '</label>';
echo elgg_view('output/longtext', array(
	'value' => $post->value,
));
echo '</div>';

echo elgg_view('input/hidden', array(
	'internalname' => 'group_guid',
	'value' => $vars['group_guid'],
));

echo elgg_view('input/hidden', array(
	'internalname' => 'user_guid',
	'value' => $post->owner_guid,
));

echo elgg_view('input/hidden', array(
	'internalname' => 'post_id',
	'value' => $vars['post_id'],
));

echo elgg_view('input/submit', array('value' => elgg_echo('submit')));

echo '</div>';
?>
<script type="text/javascript">
	$('[name=topic_guid]').change(function() {
		var $this = $(this);
		if ($this.val() == 0) {
			$('#new-topic-title').show();
		} else {
			$('#new-topic-title').hide();
		}
	});
</script>