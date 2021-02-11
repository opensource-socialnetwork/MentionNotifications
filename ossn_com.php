<?php
/**
 * Open Source Social Network
 *
 * @package   (softlab24.com).ossn
 * @author    OSSN Core Team <info@softlab24.com>
 * @copyright 2014-2018 SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      http://www.opensource-socialnetwork.org/
 */
define('MentionNotifications', ossn_route()->com . 'MentionNotifications/');
function mention_notifications_init() {
		if(ossn_isLoggedin()) {
				ossn_register_callback('comment', 'created', 'mentions_comment_created');
				ossn_register_callback('user', 'delete', 'mention_notification_user_delete');

				ossn_add_hook('notification:add', 'mention:comment:created', 'mention_notifications_notifier');
				ossn_add_hook('notification:view', 'mention:comment:created', 'mention_notifications_view_notification');

				ossn_add_hook('notification:participants', 'mention:comment:created', 'mention_notifications_participants');
		}
}
function mentions_notification_allow_tag($annotation) {
		if(!isset($annotation->type)) {
				return false;
		}
		$allow = false;
		switch($annotation->type) {
		case 'comments:post':
				$allow = true;
				break;
		case 'comments:entity':
				$entity = ossn_get_entity($annotation->subject_guid);
				switch($entity->subtype) {
				case 'file:profile:photo':
				case 'file:profile:cover':
				case 'file:ossn:aphoto':
						$allow = true;
						break;
				}
				break;
		}
		return $allow;
}
function mentions_notification_find_url($annotation, $notification) {
		if(!isset($annotation->type) || !isset($notification->subject_guid)) {
				return false;
		}
		$url = false;
		switch($annotation->type) {
		case 'comments:post':
				$url = ossn_site_url("post/view/{$notification->subject_guid}");
				break;
		case 'comments:entity':
				$entity = ossn_get_entity($annotation->subject_guid);
				switch($entity->subtype) {
				case 'file:profile:photo':
						$url = ossn_site_url("photos/user/view/{$annotation->subject_guid}");
						break;
				case 'file:profile:cover':
						$url = ossn_site_url("photos/cover/view/{$annotation->subject_guid}");
						break;
				case 'file:ossn:aphoto':
						$url = ossn_site_url("photos/view/{$annotation->subject_guid}");
						break;
				}
				break;
		}
		return $url;
}
function mention_notifications_participants() {
		return false;
}
function mention_notification_user_delete($type, $params, $data) {
		// error_log('USER ' . ossn_dump($data));
		$notifications = new OssnNotifications();
		$user          = $data['entity'];
		if(isset($user->guid)) {
				$notifications->deleteNotification(array(
						'poster_guid' => $user->guid,
						'type'        => 'mention:comment:created',
				));
		}
}
function mention_notifications_notifier($hook, $type, $return, $params) {
		$return               = $params;
		$return['owner_guid'] = $params['notification_owner'];
		return $return;
}
function mention_notifications_view_notification($hook, $type, $return, $params) {
		return ossn_plugin_view('mention_notifications/notification', $params);
}
function mentions_comment_created($callback, $type, $params) {
		$comment    = $params['value'];
		$annotation = ossn_get_annotation($params['id']);
		if(mentions_notification_allow_tag($annotation)) {
				if(!empty($comment)) {
						preg_match_all('/@(\w+)/', $comment, $matches);
						$usernames = array_unique($matches[1]);
						if($usernames) {
								foreach ($usernames as $username) {
										if($user = ossn_user_by_username($username)) {
												$notifications = new OssnNotifications();
												$notifications->add('mention:comment:created', $params['owner_guid'], $params['subject_guid'], $params['id'], $user->guid);
										}
								}
						}
				}
		}
}
ossn_register_callback('ossn', 'init', 'mention_notifications_init');