<?php

echo elgg_view('community_groups/sidebar/discussion_search');

echo elgg_view('community_groups/sidebar/howto', array('type' => 'discussion'));

echo elgg_view('community_groups/sidebar/documentation');

//featured groups
$featured_groups = elgg_get_entities_from_metadata(array('metadata_name' => 'featured_group', 'metadata_value' => 'yes', 'types' => 'group', 'limit' => 10));
echo elgg_view("groups/featured", array("featured" => $featured_groups));
