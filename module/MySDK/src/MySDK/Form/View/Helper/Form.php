<?php

namespace MySDK\Form\View\Helper;

use Zend\Form\View\Helper\Form as zForm;
use Zend\Form\FormInterface;

class Form extends zForm {

    private function renderError($form) {
        $html = '';

        if ($form->getMessages()) {
            $html .= '<div style="margin-bottom: 0!important;" class="alert alert-danger">';
            $html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
            $html .= '<ul><li>Foram encontrados um ou mais erros. Corrija os campos marcados abaixo.</li></ul></div>';
            $html .= '<br />';
        }
        return $html;
    }

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form|string
     */
    public function __invoke(FormInterface $form = null) {
        if (!$form) {
            return $this;
        }

        //add js mask
        $this->getView()->headScript()->prependFile($this->getView()->basePath('assets/veneto/assets/plugins/jquery-mask/jquery.mask.js'));

        return $this->render($form);
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form) {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $form->setAttribute('class', 'form-horizontal  form-bordered');

        $html = '<div class="panel ' . $form->getContextPanel() . '">';

        if (null != $form->getTitle() || null != $form->getSubtitle()) {
            $html .= '<div class="panel-heading">';
            if (null != $form->getTitle()) {
                $html .= '         <h4 class="panel-title">' . $form->getTitle() . '</h4>';
            }
            if (null != $form->getSubtitle()) {
                $html .= '         <p>' . $form->getSubtitle() . '</p>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="panel-body ">';
        $html .= $this->renderError($form);
        $html .= $this->getView()->form()->openTag($form);
        foreach ($form as $element) {
            $html .= $this->formField($element);
        }
        $html .= $this->getView()->form()->closeTag($form);
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function formField($element) {
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
        } elseif ($element instanceof \Zend\Form\Element\Submit) {
            return $this->getView()->formSubmit($element);
        } else {
            $classError = $this->getView()->formElementErrors($element) ? "has-error" : '';
            $cols = $element->getOption('cols') ? $element->getOption('cols') : array('col-sm-3', 'col-sm-3');
            $mask = $element->getOption('mask') ? $element->getOption('mask') : false;
//            $classRequired = $form->getInputFilter()->get($id)->isRequired() ? $form->getInputFilter()->get($id)->isRequired() : false;
//            if($classRequired){
//                $classRequired = 'required';
//            }




            $html = '<div class="form-group ' . $classError . '">';
            if ($element->getLabel() && !($element instanceof \Zend\Form\Element\Button)) {
                $html .= '<label class="control-label ' . $cols[0] . '">' . $element->getLabel() . '</label>';
            }
            $html .= "<div class='controls " . $cols[1] . "'>";
            $html .= $this->getView()->formElement($element);
            $html .= $this->getView()->formElementErrors()
                    ->setMessageOpenFormat('<span class="help-block">')
                    ->setMessageSeparatorString('<br/>')
                    ->setMessageCloseString('</span>')
                    ->render($element);
            $html .= "</div>";
            $html .= "	</div>";

            if (false != $mask) {
                $html .= "<script>";
                $html .= "$(document).ready(function(){ $('input[name=\"" . $element->getName() . "\"]').mask('" . $mask . "') });";
                $html .= "</script>";
            }
            return $html;
        }
    }

}
