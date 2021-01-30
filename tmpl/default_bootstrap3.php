<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_faq
 *
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$jinput = Factory::getApplication()->input;
$doc = Factory::getDocument();

if($jinput->get('faq'))
{
    $js = "
    jQuery(document).ready(function(){
        jQuery('#collapse".$jinput->get('faq')."').collapse('toggle');
    });
    ";
    $doc->addScriptDeclaration($js);
}

$style = '';
if($params->get('float_image'))
{
    $style .= 'float:'.$params->get('float_image').'; ';
}
if($params->get('tam_image'))
{
    $style .= 'width:'.$params->get('tam_image').'px; ';
}
if($params->get('padding_image'))
{
    $style .= 'padding:'.$params->get('padding_image').'; ';
}

$jinput = Factory::getApplication()->input;
?>
<?php if($params->get('show_search') == true) { ?>
    <form action="<?php echo Uri::current(); ?>" method="get" id="formSearch">
        <div class="input-group">
            <input type="text" value="<?php echo $jinput->get("search"); ?>" class="form-control" id="search" name="search" placeholder="<?php echo Text::_('MOD_ARTICLES_FAQ_PLACEHOLDER_SEARCH_TEXT'); ?>">
            <?php if($params->get('show_button_search') == true) { ?>
            <span class="input-group-btn">
                <input class="btn btn-default" type="reset" onclick="document.getElementById('search').value = ''; document.getElementById('formSearch').submit(); return false;"/>
                <button class="btn btn-default" type="submit"><?php echo Text::_('MOD_ARTICLES_FAQ_BUTTON_SEARCH_TEXT'); ?></button>
            </span>
            <?php } ?>
        </div>
    </form>
    <hr>
<?php } ?>
<div class="panel-group" id="accordion<?php echo $module->id; ?>" role="tablist" aria-multiselectable="true">
<?php foreach ($items as $item) : ?>
    <?php
    $text = $item->title . ' ' . $item->introtext . ' ' .$item->fulltext;
    $search = ($jinput->get("search")) ? $jinput->get("search") : '';
	$result = false;
    if(!empty($search))
    {
        $result = strpos(strtolower($text),strtolower($search));
    }
	if($result || empty($search)) {
    ?>
	<div class="panel panel-default">
        <div class="panel-heading" role="tab" id="heading<?php echo $item->id; ?>">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion<?php echo $module->id; ?>" href="#collapse<?php echo $item->id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $item->id; ?>">
                    <?php echo $item->title; ?>
                </a>
            </h4>
        </div>
        <div id="collapse<?php echo $item->id; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $item->id; ?>">
            <div class="panel-body">
                <?php if($params->get('show_image') != 'off'): ?>
                    <?php
                    $images = json_decode($item->images);
                    $image = $images->image_intro;
                    $alt = $images->image_intro_alt ? $images->image_intro_alt : $item->title;
                    if($params->get('show_image') == 'fulltext')
                    {
                        $image = $images->image_fulltext;
                        $alt = $images->image_fulltext_alt ? $images->image_fulltext_alt : $item->title;
                    }
                    ?>
                    <?php if($params->get('link_image')): ?>
                        <a href="<?php echo $item->link; ?>" itemprop="url">
                            <img style="<?php echo $style; ?>" data-src="<?php echo $image; ?>" src="<?php echo $image; ?>" alt="<?php echo $alt; ?>">
                        </a>
                    <?php else: ?>
                        <img style="<?php echo $style; ?>" data-src="<?php echo $image; ?>" src="<?php echo $image; ?>" alt="<?php echo $alt; ?>">
                    <?php endif; ?>
                <?php endif; ?>

                <?php if($params->get('show_content') != 'offc'): ?>
                    <?php
                    if($params->get('show_content') == 'partc'):
                        $cleanText = filter_var($item->introtext, FILTER_SANITIZE_STRING);
                        $introCleanText = strip_tags($cleanText);
                        if (strlen($introCleanText) > $params->get('tam_content', 200))
                        {
                            $introtext = substr($introCleanText,0,strrpos(substr($introCleanText,0,$params->get('tam_content', 200))," ")).'...';
                        }
                        else
                        {
                            $introtext = $introCleanText;
                        }
                    elseif($params->get('show_content') == 'fullc'):
                        $introtext = $item->fulltext;
                    else:
                        $introtext = $item->introtext;
                    endif;
                    ?>
                    <p><?php echo $introtext; ?></p>
                <?php endif; ?>
                
                <?php if($params->get('show_readmore')): ?>
                    <p class="text-right">
                    <?php if($params->get('readmore_behaviour')== 0): ?>
                        <a href="<?php echo $item->link; ?>" class="btn btn-primary" ><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
                    <?php elseif($params->get('readmore_behaviour')== 1): ?>
                        <a href="<?php echo $item->link; ?>" class="btn btn-primary"  target="_blank"><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
                    <?php else: ?>
                        <?php HTMLHelper::_('behavior.modal'); ?>
                        <a href="<?php echo $item->link; ?>?tmpl=component" class="btn btn-primary modal" rel="{handler: 'iframe', size: {x: <?php echo $params->get('readmore_modal_width') ?>, y: <?php echo $params->get('readmore_modal_height')?>}}"><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
                    <?php endif; ?>
                    </p> 
                <?php endif; ?>
            </div>
        </div>
	</div>
<?php } endforeach; ?>
</div>