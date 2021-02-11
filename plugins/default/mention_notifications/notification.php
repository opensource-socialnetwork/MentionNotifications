<?php
$notif   = $params;
$baseurl = ossn_site_url();
$user    = ossn_user_by_guid($notif->poster_guid);
$annotation = ossn_get_annotation($notif->item_guid);

$urlget   = mentions_notification_find_url($annotation, $notif);

if($urlget == false){
	return;	
}
$url = $urlget;

if(com_is_active('DisplayUsername')) {
		$name = $user->username;
} else {
		$name = $user->fullname;
}


$user->fullname = "<strong>{$name}</strong>";
$iconURL        = $user->iconURL()->small;

$img = "<div class='notification-image'><img src='{$iconURL}' /></div>";

if($notif->viewed !== null) {
		$viewed = '';
} elseif($notif->viewed == null) {
		$viewed = 'class="ossn-notification-unviewed"';
}
$notification_read = "{$baseurl}notification/read/{$notif->guid}?notification=" . urlencode($url);
?>
<a href='<?php echo $notification_read;?>'>
	   <li <?php echo $viewed;?>>
       <?php echo $img;?>
	   <div class='notfi-meta'> 
	   <div class='data'>
			<?php echo ossn_print("{$notif->type}", array(
					$user->fullname,
			));
			?>
		</div>
	 	</div>
    </li>
</a>
