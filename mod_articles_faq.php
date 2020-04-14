<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_faq
 *
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */

use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$items = modarticlesfaqHelper::getItems($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$layout = $params->get('layout', 'default');
switch((int)$params->get('templateframework', 1))
{
    case 2: $layout .= '_bootstrap3'; break;
    case 3: $layout .= '_uikit'; break;
}

require ModuleHelper::getLayoutPath('mod_articles_faq', $layout);