<?php 

/**

 * Template Name: Cleopatra frame page

 **/

 ?>
<?php
include_once('library.php');


$post_id=get_the_ID();

$content_post = get_post($post_id);
$content = $content_post->post_content;

$cleopatra_lib=new cleopatra_lib();
$content=$cleopatra_lib->tag_decoder($content);

echo $content;
?>



