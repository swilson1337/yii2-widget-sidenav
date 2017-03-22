<?php

namespace swilson1337\sidenav;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;
use yii\base\InvalidConfigException;
use swilson1337\sidenav\SideNavAsset;

class SideNav extends \yii\bootstrap\Nav
{
	public $activateParents = true;
	
	private $_collapseID = 0;
	
	public function init()
	{
		parent::init();
		
		SideNavAsset::register($this->view);

		Html::removeCssClass($this->options, 'nav');
		
		Html::addCssClass($this->options, 'sidenav list-group');
	}
	
	public function renderItem($item, $child = false)
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
		
		$items = ArrayHelper::getValue($item, 'items');
		
		$url = ArrayHelper::getValue($item, 'url', '#');
		
		$linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
		
		Html::addCssClass($linkOptions, ['list-item-type' => 'list-group-item']);
		
		$icon = ArrayHelper::getValue($item, 'icon', '');
		
		$active = false;
		
		if (!empty($icon))
		{
			$label = '<span class="glyphicon glyphicon-'.$icon.'"></span>&nbsp; '.$label;
		}
		
		if ($child)
		{
			$label = '&emsp;'.$label;
		}
		
		if (!empty($item['outsideUrl']))
		{
			$linkOptions['target'] = '_blank';
		}
		
		if (!empty($item['active']))
		{
			$active = true;
		}
		else
		{
			$active = $this->isItemActive($item);
		}
		
		if ($child || empty($items))
		{
			$items = '';
		}
		else
		{
			$collapseID = $this->id.'-'.$this->_collapseID;
			
			$this->_collapseID++;
			
			$label .= Html::tag('span', '', [
				'class' => 'sidenav-icon pull-right glyphicon glyphicon-chevron-'.($active ? 'up' : 'down'),
				'data-target' => $collapseID,
				'id' => $collapseID.'-toggle-button',
			]);
			
			if (empty($url) || $url == '#')
			{
				Html::addCssClass($linkOptions, 'sidenav-toggle');
				
				$linkOptions['data-target'] = $collapseID;
				
				$linkOptions['data-toggle'] = $collapseID.'-toggle-button';
			}
			
			if (is_array($items))
			{
				$items = $this->isChildActive($items, $active);
				
				$collapseOptions = ArrayHelper::getValue($item, 'collapseOptions', []);
				
				$collapseOptions['id'] = $collapseID;
				
				Html::addCssClass($collapseOptions, 'submenu panel-collapse collapse');
				
				if ($active)
				{
					Html::addCssClass($collapseOptions, 'in');
				}
				
				$items = $this->renderChildren($items, $collapseOptions);
			}
		}
		
		if ($active)
		{
			Html::addCssClass($linkOptions, 'active');
			
			if ($child)
			{
				Html::addCssStyle($linkOptions, 'background-color: #5bc0de; border-color: #5bc0de; color: #fff; border-radius: 0;');
			}
		}
		else
		{
			$label = '<span class="text-primary">'.$label.'</span>';
		}
		
		return Html::a($label, $url, $linkOptions).$items;
	}
	
	public function renderItems()
	{
		$items = [];
		
		foreach ($this->items as $item)
		{
			if (!isset($item['visible']) || $item['visible'])
			{
				$items[] = $this->renderItem($item);
			}
		}
		
		return Html::tag('div', implode("\n", $items), $this->options);
	}
	
	public function renderChildren($children, $options = [])
	{
		$items = [];
		
		foreach ($children as $item)
		{
			if (!isset($item['visible']) || $item['visible'])
			{
				$items[] = $this->renderItem($item, true);
			}
		}
		
		return Html::tag('div', implode("\n", $items), $options);
	}
	
	protected function isChildActive($items, &$active)
	{
		foreach ($items as $i => $child)
		{
			if (!empty($child['active']) || $this->isItemActive($child))
			{
				Html::addCssClass($items[$i]['options'], 'active');
				
				if ($this->activateParents)
				{
					$active = true;
				}
			}
		}
		
		return $items;
	}
}
