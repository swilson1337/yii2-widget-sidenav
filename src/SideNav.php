<?php

namespace swilson1337\sidenav;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;
use yii\base\InvalidConfigException;

class SideNav extends \yii\bootstrap\Nav
{
	public function init()
	{
		parent::init();
		
		Html::addCssClass($this->options, 'nav-pills nav-stacked');
	}
	
	public function renderItem($item)
	{
		if (is_string($item))
		{
			return $item;
		}
		
		if (!isset($item['label']))
		{
			throw new InvalidConfigException('The \'label\' option is required.');
		}
		
		$encodeLabel = (isset($item['encode']) ? $item['encode'] : $this->encodeLabels);
		
		$label = ($encodeLabel ? Html::encode($item['label']) : $item['label']);
		
		$options = ArrayHelper::getValue($item, 'options', []);
		
		$items = ArrayHelper::getValue($item, 'items');
		
		$url = ArrayHelper::getValue($item, 'url', '#');
		
		$linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
		
		$icon = ArrayHelper::getValue($item, 'icon', '');
		
		if (!empty($icon))
		{
			$icon = '<span class="glyphicon glyphicon-'.$icon.'"></span>&nbsp;';
		}
		
		if (!empty($item['outsideUrl']))
		{
			$linkOptions['target'] = '_blank';
		}
		
		if (isset($item['active']))
		{
			$active = ArrayHelper::remove($item, 'active', false);
		}
		else
		{
			$active = $this->isItemActive($item);
		}
		
		if (empty($items))
		{
			$items = '';
		}
		else
		{
			$linkOptions['data-toggle'] = 'dropdown';
			
			Html::addCssClass($options, ['widget' => 'dropdown']);
			
			Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle']);
			
			if (!empty($this->dropDownCaret))
			{
				$label .= ' '.$this->dropDownCaret;
			}
			
			if (is_array($items))
			{
				$items = $this->isChildActive($items, $active);
				
				$items = $this->renderDropdown($items, $item);
			}
		}
		
		if ($active)
		{
			Html::addCssClass($options, 'active');
		}
		
		return Html::tag('li', Html::a($icon.$label, $url, $linkOptions).$items, $options);
	}
}
