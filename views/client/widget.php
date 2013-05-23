<?php
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

if ( isset($title)  && !empty($title) )
	echo $before_title . $title . $after_title;

// TODO