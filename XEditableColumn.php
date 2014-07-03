<?php

/**
 * @inheritdoc
 */

namespace mcms\xeditable;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class XEditableColumn extends DataColumn
{
	/**
	 * @var array defaults for editable configuration
	 */
	public $pluginOptions = [];

	public $dataType = 'text';
	public $pk='id';
	public $dataTitle = '';
	public $editable = '';
	private $view = null;
	public $url = null;


	public function init()
	{
		parent::init();
		$this->registerAssets();
	}

	/**
	 * @inheritdoc
	 */
	protected function getDataCellContent($model, $key, $index)
	{
		if (empty($this->url)) {
			$this->url = \Yii::$app->urlManager->createUrl($_SERVER['REQUEST_URI']);
		}

		if (empty($this->value)) {
			$value = ArrayHelper::getValue($model, $this->attribute);
		} else {
			$value = call_user_func($this->value, $model, $index, $this);
		}

		$value = '<a href="#" data-name="'.$this->attribute.'" data-value="' . $model->{$this->attribute} . '"  class="editable" data-type="' . $this->dataType . '" data-pk="' . $model->{$this->pk} . '" data-url="' . $this->url . '" data-title="' . $this->dataTitle . '">' . $value . '</a>';

		return $value;
	}

	/**
	 * @inheritdoc
	 */
	protected function renderDataCellContent($model, $key, $index)
	{
		return $this->grid->formatter->format($this->getDataCellContent($model, $key, $index), $this->format);
	}

	/**
	 * @inheritdoc
	 */
	public function registerAssets()
	{
		$config = new XEditableConfig();

		if(isset($this->pluginOptions['mode']) && is_array($this->pluginOptions)){
			$config->mode = $this->pluginOptions['mode'];
		}

		if(isset($this->pluginOptions['form']) && is_array($this->pluginOptions)){
			$config->form = $this->pluginOptions['form'];
		}

		$config->registerDefaultAssets();

		$this->view = \Yii::$app->getView();
		XEditableAsset::register($this->view);
###################################################################                
                if($this->dataType=='date' || $this->dataType=='datetime'){//修改的源码，添加i18n，语言的支持---add i18n surpporting
                    if(isset($this->editable['datepicker'])){                        
                        $datepicker_json=  json_decode($this->editable['datepicker']);
                        if(isset($datepicker_json->language)){
                            XEditableAsset::register($this->view)->js[]='bootstrap-datepicker/js/locales/bootstrap-datepicker.'.$datepicker_json->language.'.js';
                        }
                    }
                }
########################################################################                
		$this->editable = Json::encode($this->editable);
                //every gird hold his own editable
                $this->view->registerJs("$('#".$this->grid->id." .editable').editable(".$this->editable.");");
//		$this->view->registerJs('$(".editable").editable(' . $this->editable . ');');
	}

} 
