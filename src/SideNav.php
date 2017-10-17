<?php

namespace swilson1337\sidenav;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;
use yii\base\InvalidConfigException;
use swilson1337\sidenav\SideNavAsset;
use yii\bootstrap\BootstrapPluginAsset;

class SideNav extends \yii\bootstrap\Nav
{
	public $activateParents = true;
	
	private $_collapseID = 0;
	
	public function init()
	{
		parent::init();
		
		BootstrapPluginAsset::register($this->view);
		
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
		
		Html::addCssClass($linkOptions, ['list-item-type' => 'list-group-item', 'sidenav-item']);
		
		$icon = ArrayHelper::getValue($item, 'icon', '');
		
		$active = false;
		
		$childActive = false;
		
		if (!empty($icon))
		{
			$label = '<span class="glyphicon glyphicon-'.$icon.'"></span>&nbsp; '.$label;
		}
		
		if ($child)
		{
			Html::addCssClass($linkOptions, 'sidenav-child');
			
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
		
		if ($child || empty($items) || $this->getItemCount($items) < 1)
		{
			$items = '';
		}
		else
		{
			$collapseID = $this->id.'-'.$this->_collapseID;
			
			$this->_collapseID++;
			
			$items = $this->isChildActive($items, $active, $childActive);
			
			if (empty($url) || $url == '#')
			{
				Html::addCssClass($linkOptions, 'sidenav-toggle');
				
				$linkOptions['data-target'] = $collapseID;
				
				$linkOptions['data-toggle'] = $collapseID.'-toggle-button';
			}
			
			$label .= Html::tag('span', '', [
				'class' => 'sidenav-icon glyphicon glyphicon-chevron-'.($active ? 'up' : 'down'),
				'data-target' => $collapseID,
				'id' => $collapseID.'-toggle-button',
				'style' => 'float: right;',
			]);

			$collapseOptions = ArrayHelper::getValue($item, 'collapseOptions', []);

			$collapseOptions['id'] = $collapseID;

			Html::addCssClass($collapseOptions, 'submenu panel-collapse collapse');

			if ($active)
			{
				Html::addCssClass($linkOptions, 'sidenav-parent');
				
				Html::addCssClass($collapseOptions, 'in');
			}

			$items = $this->renderChildren($items, $collapseOptions);
		}
		
		if ($url == '#' && empty($items))
		{
			return '';
		}
		elseif ($active && (!$childActive || $this->activateParents))
		{
			Html::addCssClass($linkOptions, 'active');
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
	
	public function getItemCount($items)
	{
		$i = 0;
		
		foreach ($items as $item)
		{
			if (!isset($item['visible']) || $item['visible'])
			{
				$i++;
			}
		}
		
		return $i;
	}
	
	protected function isChildActive($items, &$active, &$childActive = false)
	{
		foreach ($items as $i => $child)
		{
			if (!empty($child['active']) || $this->isItemActive($child))
			{
				Html::addCssClass($items[$i]['options'], 'active');
				
				$childActive = true;
				
				$active = true;
			}
		}
		
		return $items;
	}
}
