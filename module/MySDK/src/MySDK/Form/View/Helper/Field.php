<?php

namespace MySDK\Form\View\Helper;

use Zend\Form\View\Helper\FormInput;
use Zend\Form\ElementInterface;

class Field extends FormInput {

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormInput
     */
    public function __invoke(ElementInterface $element = null) {
        if (!$element) {
            return $this;
        }

        //add js mask
        $this->getView()->headScript()->prependFile($this->getView()->basePath('assets/veneto/assets/plugins/jquery-mask/jquery.mask.js'));

        return $this->render($element);
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element) {
        /**
         * Ser for input hidden nao gera as divs
         */
        if ($element instanceof \Zend\Form\Element\Hidden ||
                $element instanceof \Zend\Form\Element\Csrf) {
            return $this->getView()->formInput($element);
        } elseif ($element instanceof \Zend\Form\Element\Image) {
            $html = '<div class="form-group">';
            $html .= $this->getView()->formImage($element);
            $html .= '</div>';
            return $html;
            return $this->getView()->formImage($element);
        } elseif ($element instanceof \MySDK\Form\Element\Code) {
            return $element->getValue();
        }elseif($element instanceof \Zend\Form\Element\Submit){
            //@todo melhorar renderzação do field ubmite
            return '<label for=""></label><input name="' . $element->getName() . '" placeholder="" class="' . $element->getAttributes()['class'] . '" value="' . $element->getValue() . '" type="' . $element->getAttributes()['type'] . '">';

        } else {
            $gridFieldClassPrefix = 'col-xs-';
            $gridFieldSizeDefault = 2;

            $size = $element->getOption('size') ? $element->getOption('size') : $gridFieldSizeDefault;
            $classError = $this->getView()->formElementErrors($element) ? "has-error" : '';
            $inputSize = $element->getOption('inputSize') ? $element->getOption('inputSize') : '';
            $classGrid = $element->getOption('classGrid') ? $element->getOption('classGrid') : '';
            $classFormGroup = $element->getOption('classFormGroup') ? $element->getOption('classFormGroup') : '';
            $getLabal = $element->getLabel();
            $getType = $element->getAttributes()['type'];
            $getName = $element->getAttributes()['name'];
            $getPlaceholder = $element->getAttributes()['placeholder']?$element->getAttributes()['placeholder']:'';
            $getClass = $element->getAttributes()['class'];
            $mask = $element->getOption('mask') ? $element->getOption('mask') : false;



            $html = '';
         //   $html .= '    <div class="' . $gridFieldClassPrefix . $size . ' ' . $classGrid . '">';
            $html .='<div class="row">';
            $html .='<div class="' . $gridFieldClassPrefix . $size . '">';

            $html .= '        <div class="form-group ' . ' ' . $classFormGroup . ' ' . $inputSize . ' ' . $classError . '">';

            $html .= '<label for="' . $getName . '">'.$getLabal.'</label>';
            $html .= '<input type="' . $getType . '" name="' . $getName . '" placeholder="' . $getPlaceholder . '" class="' . $getClass . '" value="">';

            $html .= $this->getView()->formElementErrors()
                ->setMessageOpenFormat('<span class="help-block">')
                ->setMessageSeparatorString('<br/>')
                ->setMessageCloseString('</span>')
                ->render($element);

            $html .= '        </div>';
            $html .= '        </div>';
            $html .= '        </div>';
//            $html .= '    </div>';

            if (false != $mask) {
                $html .= "<script>";
                $html .= "$(document).ready(function(){ $('input[name=\"" . $getName . "\"]').mask('" . $mask . "') });";
                $html .= "</script>";
            }

            return $html;
        }
    }

    public function renderElement(ElementInterface $element) {
        $html = '';
        if ($element->getLabel()) {
            $html .= $this->getView()->formLabel($element);
           // $html .= '        <label for="'.$this->getView()->formLabel($element).'" class="control-label '.'reqiurid'.'">' . $this->getView()->formLabel($element) . '</label>';
        }
        $html .= $this->getView()->formElement($element);
        $html .= $this->getView()->formElementErrors()
                ->setMessageOpenFormat('<span class="help-block" style="color:#c23527;">')
                ->setMessageSeparatorString('<br/>')
                ->setMessageCloseString('</span>')
                ->render($element);
        return $html;
    }

}
