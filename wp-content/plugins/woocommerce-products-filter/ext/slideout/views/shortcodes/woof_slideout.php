<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
$style="background-size: ".$image_h."px ".$image_w."px !important;";
$style_cont="";
if($height!="auto"){
    $style_cont.="height:".$height.";";
}
if($width!="auto"){
    $style_cont.="width:".$width.";";
}
$key=uniqid("woof_");        
?>
<style>
    .woof-slide-out-div .woof-handle{
        <?php echo $style ?>
    }
    <?php if($style_cont){ ?>
    .woof-slide-content.woof-slide-<?php echo $key ?>{
        <?php echo $style_cont ?>
    }
    <?php }?>
</style>
<div class="woof-slide-out-div <?php echo $class ?>" data-key="<?php echo $key ?>"  data-image="<?php echo $image ?>" 
     data-image_h="<?php echo $image_h ?>" data-image_w="<?php echo $image_w ?>" 
     data-mobile="<?php echo $mobile_behavior ?>"  data-action="<?php echo  $action ?>" data-location="<?php echo  $location ?>" 
     data-speed="<?php echo  $speed ?>" data-toppos="<?php echo  $offset ?>"  data-onloadslideout="<?php echo  $onloadslideout ?>"
     data-height="<?php echo  $height ?>" data-width="<?php echo  $width ?>">
    <span class="woof-handle <?php echo $key ?>" style="" ><?php if($image=="null"){ echo $text; } ?></span>
    <div class="woof-slide-content woof-slide-<?php echo $key ?>">
        <?php echo  do_shortcode($content) ?>
    </div>    
</div> 

