<?php

namespace swilson1337\sidenav;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use Yii;
use swilson1337\sidenav\SideNavAsset;

class SideNav extends \kartik\sidenav\SideNav
{
	public $indItem = '';
	
	public $outsideLinkTemplate = '<a href="{url}" target="_blank">{icon}{label}</a>';
	
	public function init()
	{
		parent::init();
		
		SideNavAsset::register($this->view);
	}
	
	protected function renderItem($item)
	{
		$this->validateItems($item);
		
		$template = ArrayHelper::getValue($item, 'template', (!empty($item['outsideUrl']) ? $this->outsideLinkTemplate : $this->linkTemplate));
		
		$url = Url::to(ArrayHelper::getValue($item, 'url', '#'));
		
		if (empty($item['top']))
		{
			if (empty($item['items']))
			{
				$template = str_replace('{icon}', $this->indItem.'{icon}', $template);
			}
			else
			{
				if ($item['active'])
				{
					$template = str_replace('{icon}', Html::tag('span', $this->indMenuOpen, ['class' => 'opened sidenav-toggle']).Html::tag('span', $this->indMenuClose, ['class' => 'closed sidenav-toggle', 'style' => 'display: none;']).'{icon}', $template);
				}
				else
				{
					$template = str_replace('{icon}', Html::tag('span', $this->indMenuClose, ['class' => 'closed sidenav-toggle']).Html::tag('span', $this->indMenuOpen, ['class' => 'opened sidenav-toggle', 'style' => 'display: none;']).'{icon}', $template);
				}
			}
		}
		
		$icon = (empty($item['icon']) ? '' : ('<span class="'.$this->iconPrefix.$item['icon'].'"></span> &nbsp;'));
		
		unset($item['icon'], $item['top']);
		
		return strtr($template, [
			'{url}' => $url,
			'{label}' => $item['label'],
			'{icon}' => $icon,
		]);
	}
	
	protected function renderItems($items)
	{
		$n = count($items);
		
		$lines = [];
		
		foreach ($items as $i => $item)
		{
			$options = ArrayHelper::merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
			
			$tag = ArrayHelper::remove($options, 'tag', 'li');
			
			$class = [];
			
			if ($item['active'])
			{
				$active = true;
				
				if (!empty($item['items']))
				{
					foreach ($item['items'] as $child)
					{
						if ($child['active'])
						{
							$active = false;
							
							break;
						}
					}
				}
				
				if ($active)
				{
					$class[] = $this->activeCssClass;
				}
				else
				{
					$class[] = 'active-parent';
				}
			}
			
			if ($i === 0 && $this->firstItemCssClass !== null)
			{
				$class[] = $this->firstItemCssClass;
			}
			
			if ($i === ($n - 1) && $this->lastItemCssClass !== null)
			{
				$class[] = $this->lastItemCssClass;
			}
			
			if (!empty($class))
			{
				if (empty($options['class']))
				{
					$options['class'] = implode(' ', $class);
				}
				else
				{
					$options['class'] .= ' '.implode(' ', $class);
				}
			}
			
			$menu = $this->renderItem($item);
			
			if (!empty($item['items']))
			{
				$submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
				
				$menu .= strtr($submenuTemplate, [
					'{items}' => $this->renderItems($item['items']),
				]);
			}
			
			$lines[] = Html::tag($tag, $menu, $options);
		}
		
		return implode("\n", $lines);
	}
}
