<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace TwitterBootstrap\Form\View\Helper;

use Zend\Filter\MonthSelect;
use Zend\Form\Element\Button;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Submit;
use Zend\Form\ElementInterface;
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormRow as BaseFormRow;
use Zend\Stdlib\ArrayUtils;

class FormRow extends BaseFormRow
{
    /**
     * @var string
     */
    protected $inputButtonClass = 'btn';

    /**
     * @var string
     */
    protected $inputFormControlClass = 'form-control';

    /**
     * @return string
     */
    public function getInputButtonClass(): string
    {
        return $this->inputButtonClass;
    }

    /**
     * @param string $inputButtonClass
     * @return BootstrapRow
     */
    public function setInputButtonClass(string $inputButtonClass): BootstrapRow
    {
        $this->inputButtonClass = $inputButtonClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputFormControlClass(): string
    {
        return $this->inputFormControlClass;
    }

    /**
     * @param string $inputFormControlClass
     * @return BootstrapRow
     */
    public function setInputFormControlClass(string $inputFormControlClass): BootstrapRow
    {
        $this->inputFormControlClass = $inputFormControlClass;
        return $this;
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ElementInterface $element
     * @param  null|string $labelPosition
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $inputFormControlClass = $this->getInputFormControlClass();
        $inputButtonClass = $this->getInputButtonClass();

        $element->setOptions(ArrayUtils::merge(
            $element->getOptions(),
            ['label_attributes' => ['class' => 'control-label']]
        ));

        if (is_null($labelPosition)) {
            $labelPosition = $this->labelPosition;
        }

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        // Does this element have errors ?
        if (count($element->getMessages()) > 0 && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        // twitter bootstrap specific
        $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');

        if ($element instanceof Button || $element instanceof Submit) {
            $classAttributes = $classAttributes . $inputButtonClass;
        } else {
            $classAttributes = $classAttributes . $inputFormControlClass;

            // label for=""
            if (!$element->hasAttribute('id')) {
                $element->setAttribute('id', md5($element->getName()));
            }
        }

        $element->setAttribute('class', $classAttributes);

        if ($this->partial) {
            $vars = [
                'element' => $element,
                'label' => $label,
                'labelAttributes' => $this->labelAttributes,
                'labelPosition' => $labelPosition,
                'renderErrors' => $this->renderErrors,
            ];

            return $this->view->render($this->partial, $vars);
        }

        if ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element);
        }

        $elementString = $elementHelper->render($element);

        $hideElement = '';
        if ($element->getAttribute('hidden')) {
            $hideElement = 'display:none';
        }

        // twitter bootstrap specific
        $formGroupOpen = '<div class="form-group" style="' . $hideElement . '">';
        $formGroupClose = '</div>';

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');
        if (isset($label) && '' !== $label && $type !== 'hidden') {
            $labelAttributes = [];

            if ($element instanceof LabelAwareInterface) {
                $labelAttributes = $element->getLabelAttributes();
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            if ($type === 'multi_checkbox'
                || $type === 'radio'
                || $element instanceof MonthSelect
                || $element instanceof Captcha
            ) {
                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $elementString
                );
            } else {
                // Ensure element and label will be separated if element has an `id`-attribute.
                // If element has label option `always_wrap` it will be nested in any case.
                if ($element->hasAttribute('id')
                    && ($element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap'))
                ) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
                } else {
                    $labelOpen = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }

                if ($label !== '' && (!$element->hasAttribute('id'))
                    || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
                ) {
                    $label = '<span>' . $label . '</span>';
                }

                // Button element is a special case, because label is always rendered inside it
                if ($element instanceof Button) {
                    $labelOpen = $labelClose = $label = '';
                }

                if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position')) {
                    $labelPosition = $element->getLabelOption('label_position');
                }

                switch ($labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $formGroupOpen . $labelOpen . $label . $labelClose . $elementString . $formGroupClose;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $formGroupOpen . $elementString . $labelOpen . $label . $labelClose . $formGroupClose;
                        break;
                }
            }

            if ($this->renderErrors) {
                $markup .= $elementErrors ?? '';
            }
        } else {
            if ($this->renderErrors) {
                $markup = $formGroupOpen . $elementString . ($elementErrors ?? '') . $formGroupClose;
            } else {
                $markup = $formGroupOpen . $elementString . $formGroupClose;
            }
        }

        return $markup;
    }
}
