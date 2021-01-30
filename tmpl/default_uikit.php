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

$doc = Factory::getDocument();
$doc->addScript('https://getuikit.com/src/js/components/accordion.js');

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
    <form action="<?php echo Uri::current(); ?>" method="get" class="uk-form" id="formSearch">
        <div class="uk-inline">
            <input type="text" name="search" value="<?php echo $jinput->get("search"); ?>" id="search" placeholder="<?php echo Text::_('MOD_ARTICLES_FAQ_PLACEHOLDER_SEARCH_TEXT'); ?>">
            <?php if($params->get('show_button_search') == true) { ?>
                <input class="uk-button" type="reset" onclick="document.getElementById('search').value = ''; document.getElementById('formSearch').submit(); return false;"/>
                <button type="submit" class="uk-button"><?php echo Text::_('MOD_ARTICLES_FAQ_BUTTON_SEARCH_TEXT'); ?></button>
            <?php } ?>
        </div>
    </form>
    <hr>
<?php } ?>
<div class="uk-accordion" data-uk-accordion>
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
	<h3 class="uk-accordion-title"><?php echo $item->title; ?></h3>
    <div class="uk-accordion-content">
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
                    $introtext = '<p>'.$introCleanText.'</p>';
                }
            elseif($params->get('show_content') == 'fullc'):
                $introtext = $item->fulltext;
            else:
                $introtext = $item->introtext;
            endif;
            ?>
            <?php echo $introtext; ?>
        <?php endif; ?>
        
        <?php if($params->get('show_readmore')): ?>
            <p class="text-right">
            <?php if($params->get('readmore_behaviour')== 0): ?>
                <a href="<?php echo $item->link; ?>" class="uk-button uk-button-primary" ><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
            <?php elseif($params->get('readmore_behaviour')== 1): ?>
                <a href="<?php echo $item->link; ?>" class="uk-button uk-button-primary"  target="_blank"><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
            <?php else: ?>
                <?php HTMLHelper::_('behavior.modal'); ?>
                <a href="<?php echo $item->link; ?>?tmpl=component" class="uk-button uk-button-primary modal" rel="{handler: 'iframe', size: {x: <?php echo $params->get('readmore_modal_width') ?>, y: <?php echo $params->get('readmore_modal_height')?>}}"><?php echo $params->get('readmore_text') ? $params->get('readmore_text') : Text::_('MOD_ARTICLES_FAQ_FIELD_READMORE_TEXT'); ?></a>
            <?php endif; ?>
            </p> 
        <?php endif; ?>
	</div>
<?php } endforeach; ?>
</div>

<?php if($params->get('script')): ?>
<script>
(function(addon) {
    var component;

    if (window.UIkit) {
        component = addon(UIkit);
    }

    if (typeof define == 'function' && define.amd) {
        define('uikit-accordion', ['uikit'], function(){
            return component || addon(UIkit);
        });
    }
})(function(UI){

    "use strict";

    UI.component('accordion', {

        defaults: {
            showfirst  : false,
            collapse   : false,
            animate    : true,
            easing     : 'swing',
            duration   : 300,
            toggle     : '.uk-accordion-title',
            containers : '.uk-accordion-content',
            clsactive  : 'uk-active'
        },

        boot:  function() {

            // init code
            UI.ready(function(context) {

                setTimeout(function(){

                    UI.$('[data-uk-accordion]', context).each(function(){

                        var ele = UI.$(this);

                        if (!ele.data('accordion')) {
                            UI.accordion(ele, UI.Utils.options(ele.attr('data-uk-accordion')));
                        }
                    });

                }, 0);
            });
        },

        init: function() {

            var $this = this;

            this.element.on('click.uk.accordion', this.options.toggle, function(e) {

                e.preventDefault();

                $this.toggleItem(UI.$(this).data('wrapper'), $this.options.animate, $this.options.collapse);
            });

            this.update(true);

            UI.domObserve(this.element, function(e) {
                if ($this.element.children($this.options.containers).length) {
                    $this.update();
                }
            });
        },

        toggleItem: function(wrapper, animated, collapse) {

            var $this = this;

            wrapper.data('toggle').toggleClass(this.options.clsactive);
            wrapper.data('content').toggleClass(this.options.clsactive);

            var active = wrapper.data('toggle').hasClass(this.options.clsactive);

            if (collapse) {
                this.toggle.not(wrapper.data('toggle')).removeClass(this.options.clsactive);
                this.content.not(wrapper.data('content')).removeClass(this.options.clsactive)
                    .parent().stop().css('overflow', 'hidden').animate({ height: 0 }, {easing: this.options.easing, duration: animated ? this.options.duration : 0}).attr('aria-expanded', 'false');
            }

            wrapper.stop().css('overflow', 'hidden');

            if (animated) {

                wrapper.animate({ height: active ? getHeight(wrapper.data('content')) : 0 }, {easing: this.options.easing, duration: this.options.duration, complete: function() {

                    if (active) {
                        wrapper.css({'overflow': '', 'height': 'auto'});
                        UI.Utils.checkDisplay(wrapper.data('content'));
                    }

                    $this.trigger('display.uk.check');
                }});

            } else {

                wrapper.height(active ? 'auto' : 0);

                if (active) {
                    wrapper.css({'overflow': ''});
                    UI.Utils.checkDisplay(wrapper.data('content'));
                }

                this.trigger('display.uk.check');
            }

            // Update ARIA
            wrapper.attr('aria-expanded', active);

            this.element.trigger('toggle.uk.accordion', [active, wrapper.data('toggle'), wrapper.data('content')]);
        },

        update: function(init) {

            var $this = this, $content, $wrapper, $toggle;

            this.toggle = this.find(this.options.toggle);
            this.content = this.find(this.options.containers);

            this.content.each(function(index) {

                $content = UI.$(this);

                if ($content.parent().data('wrapper')) {
                    $wrapper = $content.parent();
                } else {
                    $wrapper = UI.$(this).wrap('<div data-wrapper="true" style="overflow:hidden;height:0;position:relative;"></div>').parent();

                    // Init ARIA
                    $wrapper.attr('aria-expanded', 'false');
                }

                $toggle = $this.toggle.eq(index);

                $wrapper.data('toggle', $toggle);
                $wrapper.data('content', $content);
                $toggle.data('wrapper', $wrapper);
                $content.data('wrapper', $wrapper);
            });

            this.element.trigger('update.uk.accordion', [this]);

            if (init && this.options.showfirst) {
                this.toggleItem(this.toggle.eq(0).data('wrapper'), false, false);
            }
        }

    });

    // helper

    function getHeight(ele) {

        var $ele = UI.$(ele), height = "auto";

        if ($ele.is(":visible")) {
            height = $ele.outerHeight();
        } else {

            var tmp = {
                position   : $ele.css('position'),
                visibility : $ele.css('visibility'),
                display    : $ele.css('display')
            };

            height = $ele.css({position: 'absolute', visibility: 'hidden', display: 'block'}).outerHeight();

            $ele.css(tmp); // reset element
        }

        return height;
    }

    return UI.accordion;
});
</script>
<?php endif; ?>