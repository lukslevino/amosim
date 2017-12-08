<?php

namespace MySDK\Form\View\Helper;

use Zend\Form\View\Helper\Form as zForm;
use MySDK\Form\Form;
use Zend\Form\FormInterface;

class FormBasic extends zForm {

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

    private function renderError(Form $form) {
        $html = '';
        if ($form->getMessages()) {
            $html .= '<div style="margin-bottom: 0!important;" class="alert alert-danger">';
            $html .= '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
            $html .= '<ul><li>Foram encontrados um ou mais erros. Corrija os campos marcados abaixo.</li></ul></div>';
        }
        return $html;
    }

    private function renderPanelHeader(Form $form) {
        $html = '';
        $html .= '<div class="panel ' . $form->getContextPanel() . '">';
        if (null != $form->getTitle() || null != $form->getSubtitle()) {
            $html .= '    <div class="panel-heading">';
            if (null != $form->getTitle()) {
                $html .= '         <h4 class="panel-title">' . $form->getTitle() . '</h4>';
            }
            if (null != $form->getSubtitle()) {
                $html .= '         <p>' . $form->getSubtitle() . '</p>';
            }
            $html .= '    </div>';
        }
        return $html;
    }

    private function renderField(Form $form, $id) {
        $element = $form->get($id);

        $size = $element->getOption('size') ? $element->getOption('size') : $form->getGridFieldSizeDefault();
        $classError = $this->getView()->formElementErrors($element) ? "has-error" : '';
        $inputSize = $element->getOption('inputSize') ? $element->getOption('inputSize') : $form->getInputSizeDefault();
        $classGrid = $element->getOption('classGrid') ? $element->getOption('classGrid') : '';
        $classFormGroup = $element->getOption('classFormGroup') ? $element->getOption('classFormGroup') : '';
        $classRequired = $form->getInputFilter()->get($id)->isRequired() ? $form->getInputFilter()->get($id)->isRequired() : false;
        if($classRequired){
            $classRequired = 'required';
        }

        $mask = $element->getOption('mask') ? $element->getOption('mask') : false;
        $html = '';
        $html .= '    <div class="' . $form->getGridFieldClassPrefix() . $size . ' ' . $classGrid . '">';
        $html .= '        <div class="form-group ' . ' ' . $classFormGroup . ' ' . $inputSize . ' ' . $classError . '">';


        if ($element->getLabel()) {
            $html .= '        <label for="'.$element->getName().'" class="control-label '.$classRequired.'">' . $element->getLabel() . '</label>';
        }
        $html .= $this->getView()->formElement($element);
        $html .= $this->getView()->formElementErrors()
                ->setMessageOpenFormat('<span class="help-block">')
                ->setMessageSeparatorString('<br/>')
                ->setMessageCloseString('</span>')
                ->render($element);
        $html .= '        </div>';
        $html .= '    </div>';

        if (false != $mask) {
            $html .= "<script>";
            $html .= "$(document).ready(function(){ $('input[name=\"" . $element->getName() . "\"]').mask('" . $mask . "') });";
            $html .= "</script>";
        }

        return $html;
    }

    private function renderPanelBody(Form $form) {
        $html = '';
        $html .= '    <div class="panel-body">';
        $elementsRodape = array();
        $isFirstRow = true;
        $colsSize = 0;
        $elements = $form->getElements();
        foreach ($elements as $k => $element) {
            /**
             * Se o tipo de campo for hidden ou Csrf, imprime o input e passa para o proximo
             */
            if ($element instanceof \Zend\Form\Element\Hidden ||
                    $element instanceof \Zend\Form\Element\Csrf) {
                $html .= $this->getView()->formInput($element);
                continue;
            }

            $isRodape = $element->getOption('isFooter') ? $element->getOption('isFooter') : false;
            /**
             * Se for submit ou button, add em uma variavel para imprimir no rodape
             */
            if ($isRodape) {
                $elementsRodape[] = $element;
                continue;
            }

            //Recupera o tamanho do campo se nao tiver tamanho assume o default 3
            $size = $element->getOption('size') ? $element->getOption('size') : $form->getGridFieldSizeDefault();
            $clear = $element->getOption('clear') ? $element->getOption('clear') : false;
            //Soma com os campos que ja foram impresso
            $colsSize += $size;

            //Veririca se existe necessidade de outra linha [12 tamanho maximo de colunas do bootstrap]
            if ($colsSize >= 12 || $clear) {
                $html .= '</div>';
                $html .= '<div class="row">';
                $colsSize = $size;
            }


            //Abre uma nova linha
            if ($isFirstRow) {
                $html .= '<div class="row">';
                $isFirstRow = false;
            }

            if ($element instanceof \MySDK\Form\Element\Code) {
                if (false == $isFirstRow) {
                    $html .= '</div>';
                }
                $colsSize = 0;
                $html .= $element->getValue();
                continue;
            }


            $html .= $this->renderField($form, $k);

        }
        if ($isFirstRow == false) {
            $html .= '    </div>';
        }
        $html .= '    </div>';

        $html .= $this->renderPanelRodape($form, $elementsRodape);
        return $html;
    }

    private function renderPanelRodape(Form $form, array $elementsRodape = array()) {
        $size = $form->getGridSizeRodape();
        $html = '';
        $html .= '    <div class="panel-footer">';
        $html .= '        <div class="row">';
        $html .= '                <div class="' . $form->getGridFieldClassPrefix() . $size . '">';

        foreach ($elementsRodape as $k => $element) {

                $html .= $this->getView()->formInput($element);

        }
        $html .= '                </div>';
        $html .= '        </div>';
        $html .= '    </div>';
        return $html;
    }

    public function renderPanel(Form $form) {
        $html = '';
        $html .= $this->renderPanelHeader($form);
        $html .= $this->renderPanelBody($form);
        return $html;
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
        $html = '';
        $html .= $this->renderError($form);
        $html .= $this->getView()->form()->openTag($form);
        $html .= $this->renderPanel($form);
        $html .= $this->getView()->form()->closeTag($form);
        return $html;
    }

}
